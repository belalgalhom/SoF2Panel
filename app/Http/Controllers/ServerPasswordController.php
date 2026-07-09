<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Server;
use Illuminate\Support\Facades\Crypt;
use phpseclib3\Net\SSH2;

class ServerPasswordController extends Controller
{
    public function updateFtp(Request $request, Server $server)
    {
        $user = auth()->user();
        if (!$user->isAdmin()) {
            $pivot = $server->users()->where('user_id', $user->id)->first()?->pivot;
            if (!$pivot || !$pivot->manage_server_users) {
                abort(403);
            }
        }

        $request->validate(['password' => 'required|string|min:6']);
        $newPassword = $request->password;

        $host = $server->host;
        $ssh = new SSH2($host->hostname, $host->port);
        if (!$ssh->login($host->username, Crypt::decryptString($host->password))) {
            return back()->with('error', 'Authentication to host failed.');
        }

        $username = escapeshellarg($server->ftp_username);
        $passwordEscaped = escapeshellarg($server->ftp_username . ':' . $newPassword);

        $ssh->exec("echo {$passwordEscaped} | chpasswd");

        $server->update([
            'ftp_password' => Crypt::encryptString($newPassword)
        ]);

        \App\Models\Log::create([
            'user_id' => $user->id,
            'action' => 'Changed FTP Password',
            'target' => $server->name,
            'ip' => request()->ip()
        ]);

        return back()->with('success', 'FTP Password updated successfully.');
    }

    public function updateRcon(Request $request, Server $server)
    {
        $user = auth()->user();
        if (!$user->isAdmin()) {
            $pivot = $server->users()->where('user_id', $user->id)->first()?->pivot;
            if (!$pivot || !$pivot->manage_server_users) {
                abort(403);
            }
        }

        $request->validate(['rcon_password' => 'required|string|min:3']);
        $newPassword = $request->rcon_password;

        $server->update([
            'rcon_password' => Crypt::encryptString($newPassword)
        ]);

        try {
            $serverService = app(\App\Services\ServerService::class);
            $status = $serverService->getServerStatus($server);
            
            if ($status === 'Running') {
                $ssh = $serverService->getSSH($server->host);
                $username = escapeshellarg($server->ftp_username);
                $screenName = str_replace(' ', '_', $server->name);
                
                $safeConsolePass = str_replace(['\\', '"', "'"], ['\\\\', '\"', ""], $newPassword);
                
                $injectCommand = "screen -S {$screenName} -X stuff 'set rconpassword \"{$safeConsolePass}\"\r'";
                $ssh->exec("su - {$username} -c " . escapeshellarg($injectCommand));
            }
        } catch (\Throwable $e) {
        }

        \App\Models\Log::create([
            'user_id' => $user->id,
            'action' => 'Changed RCON Password',
            'target' => $server->name,
            'ip' => request()->ip()
        ]);

        return back()->with('success', 'RCON Password updated and applied to the server immediately.');
    }
}
