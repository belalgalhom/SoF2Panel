@extends('layouts.app')

@section('title', 'User Management')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h2 style="margin: 0;">Manage Users</h2>
        <button class="btn btn-primary" style="width: auto;" onclick="document.getElementById('addUserModal').style.display='flex'">
            <i data-feather="plus" style="width: 16px; height: 16px; margin-right: 0.5rem;"></i> Add User
        </button>
    </div>

<div class="table-container">
    <div class="table-header">
        <h3 style="margin: 0;">Registered Users</h3>
    </div>

    <div class="table-responsive">
        <table class="panel-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Created</th>
                <th style="text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->username }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @php
                            $badgeColor = 'var(--primary)';
                            $badgeBg = 'var(--primary)20';
                            $displayRole = ucfirst($user->role);
                            
                            if (!$user->status) {
                                $badgeColor = 'var(--text-muted)';
                                $badgeBg = 'rgba(255,255,255,0.1)';
                                $displayRole = 'Disabled';
                            } elseif ($user->role === 'admin') {
                                $badgeColor = 'var(--danger)';
                                $badgeBg = 'var(--danger)20';
                            }
                        @endphp
                        <span class="status-badge" style="background: {{ $badgeBg }}; color: {{ $badgeColor }}">
                            {{ $displayRole }}
                        </span>
                    </td>
                    <td>{{ $user->created_at->format('Y-m-d') }}</td>
                    <td style="text-align: right;">
                        <div style="display: inline-flex; gap: 0.5rem;">
                            <button type="button" class="btn btn-primary" style="background: rgba(99, 102, 241, 0.1); color: var(--primary); border: 1px solid rgba(99, 102, 241, 0.2); padding: 0.5rem; width: auto;" onclick="openEditModal({{ $user->id }}, '{{ $user->username }}', '{{ $user->email }}', '{{ $user->role }}', {{ $user->status ? 'true' : 'false' }})" title="Edit User">
                                <i data-feather="edit-2" style="width: 16px; height: 16px;"></i>
                            </button>
                            @if($user->id !== auth()->id())
                                <form action="{{ route('users.destroy', $user) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-primary" style="background: rgba(239, 68, 68, 0.1); color: var(--danger); border: 1px solid rgba(239, 68, 68, 0.2); padding: 0.5rem; width: auto;" title="Delete User">
                                        <i data-feather="trash-2" style="width: 16px; height: 16px;"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: var(--text-muted);">No users found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
        </div>

    <div style="margin-top: 1rem;">
        {{ $users->links('pagination::simple-bootstrap-4') }}
    </div>
</div>

<!-- Add User Modal -->
<div id="addUserModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 100; justify-content: center; align-items: center; backdrop-filter: var(--glass-blur);">
    <div class="glass-panel" style="max-height: 90vh; overflow-y: auto;">
        <h3 style="margin-bottom: 1.5rem;">Add New User</h3>
        
        <form method="POST" action="{{ route('users.store') }}">
            @csrf
            
            <div class="form-group">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-input" required>
            </div>

            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-input" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-input" required minlength="8">
            </div>

            <div class="form-group">
                <select name="role" class="form-input" required>
                    <option value="user">User</option>
                    <option value="admin">Administrator</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" class="form-input" required>
                    <option value="1">Active</option>
                    <option value="0">Disabled</option>
                </select>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">Create User</button>
                <button type="button" class="btn" style="flex: 1; border: 1px solid var(--border); color: var(--text-main); background: transparent;" onclick="document.getElementById('addUserModal').style.display='none'">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 100; justify-content: center; align-items: center; backdrop-filter: var(--glass-blur);">
    <div class="glass-panel" style="max-height: 90vh; overflow-y: auto;">
        <h3 style="margin-bottom: 1.5rem;">Edit User</h3>
        
        <form method="POST" action="" id="editUserForm">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label class="form-label">Username</label>
                <input type="text" name="username" id="edit_username" class="form-input" required>
            </div>

            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" id="edit_email" class="form-input" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">New Password <small style="color: var(--text-muted);">(Leave blank to keep current)</small></label>
                <input type="password" name="password" class="form-input" minlength="8">
            </div>

            <div class="form-group" id="edit_role_group">
                <label class="form-label">Role</label>
                <select name="role" id="edit_role" class="form-input">
                    <option value="user">User</option>
                    <option value="admin">Administrator</option>
                </select>
            </div>

            <div class="form-group" id="edit_status_group">
                <label class="form-label">Status</label>
                <select name="status" id="edit_status" class="form-input">
                    <option value="1">Active</option>
                    <option value="0">Disabled</option>
                </select>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">Save Changes</button>
                <button type="button" class="btn" style="flex: 1; border: 1px solid var(--border); color: var(--text-main); background: transparent;" onclick="document.getElementById('editUserModal').style.display='none'">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditModal(id, username, email, role, status) {
        document.getElementById('editUserForm').action = '/users/' + id;
        document.getElementById('edit_username').value = username;
        document.getElementById('edit_email').value = email;
        
        document.getElementById('edit_role').value = role;
        document.getElementById('edit_status').value = status ? '1' : '0';
        
        if (id == {{ auth()->id() }}) {
            document.getElementById('edit_role_group').style.display = 'none';
            document.getElementById('edit_status_group').style.display = 'none';
        } else {
            document.getElementById('edit_role_group').style.display = 'block';
            document.getElementById('edit_status_group').style.display = 'block';
        }

        document.getElementById('editUserModal').style.display = 'flex';
    }
</script>
@endsection
