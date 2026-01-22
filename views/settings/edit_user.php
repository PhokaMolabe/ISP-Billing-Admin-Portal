<div class="row">
    <div class="col-md-12">
        <h1><i class="fa fa-edit"></i> Edit User</h1>
        <ol class="breadcrumb">
            <li><a href="?_route=dashboard">Dashboard</a></li>
            <li><a href="?_route=settings/users-list">User Management</a></li>
            <li class="active">Edit User</li>
        </ol>
    </div>
</div>

<div class="row">
    <!-- Profile Panel (Left) -->
    <div class="col-md-6">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-user"></i> Profile</h3>
            </div>
            <div class="panel-body">
                <?php if (isset($errors) && $errors): ?>
                    <div class="alert alert-danger">
                        <ul class="list-unstyled">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form class="form-horizontal" method="POST" action="?_route=settings/users-edit-post/<?php echo $targetUser['id']; ?>">
                    <?php echo $csrf_field; ?>
                    
                    <div class="form-group">
                        <label for="username" class="col-sm-4 control-label">Username</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="username" value="<?php echo htmlspecialchars($targetUser['username']); ?>" readonly>
                            <span class="help-block">Username cannot be changed</span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="fullname" class="col-sm-4 control-label">Full Name *</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="fullname" name="fullname" value="<?php echo htmlspecialchars($targetUser['fullname']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="col-sm-4 control-label">Email</label>
                        <div class="col-sm-8">
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($targetUser['email'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone" class="col-sm-4 control-label">Phone</label>
                        <div class="col-sm-8">
                            <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($targetUser['phone'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="user_type" class="col-sm-4 control-label">User Type</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="user_type" value="<?php echo htmlspecialchars($targetUser['user_type']); ?>" readonly>
                            <span class="help-block">User type cannot be changed</span>
                        </div>
                    </div>
                    
                    <?php if ($user['user_type'] === USER_TYPE_SUPERADMIN || $user['user_type'] === USER_TYPE_ADMIN): ?>
                        <div class="form-group">
                            <label for="status" class="col-sm-4 control-label">Status</label>
                            <div class="col-sm-8">
                                <select class="form-control" id="status" name="status">
                                    <option value="active" <?php echo $targetUser['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                                    <option value="inactive" <?php echo $targetUser['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>
                        </div>
                        
                        <?php if ($targetUser['user_type'] === USER_TYPE_SALES && !empty($availableAgents)): ?>
                            <div class="form-group">
                                <label for="root" class="col-sm-4 control-label">Assign to Agent</label>
                                <div class="col-sm-8">
                                    <select class="form-control" id="root" name="root">
                                        <option value="">No Agent</option>
                                        <?php foreach ($availableAgents as $agent): ?>
                                            <option value="<?php echo $agent['id']; ?>" <?php echo $targetUser['root'] == $agent['id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($agent['fullname']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <div class="col-sm-offset-4 col-sm-8">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Save Changes
                            </button>
                            <a href="?_route=dashboard" class="btn btn-default">
                                <i class="fa fa-times"></i> Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Credentials Panel (Right) -->
    <div class="col-md-6">
        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-key"></i> Credentials</h3>
            </div>
            <div class="panel-body">
                <form class="form-horizontal" method="POST" action="?_route=settings/users-edit-post/<?php echo $targetUser['id']; ?>">
                    <?php echo $csrf_field; ?>
                    
                    <input type="hidden" name="fullname" value="<?php echo htmlspecialchars($targetUser['fullname']); ?>">
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($targetUser['email'] ?? ''); ?>">
                    <input type="hidden" name="phone" value="<?php echo htmlspecialchars($targetUser['phone'] ?? ''); ?>">
                    <?php if ($user['user_type'] === USER_TYPE_SUPERADMIN || $user['user_type'] === USER_TYPE_ADMIN): ?>
                        <input type="hidden" name="status" value="<?php echo htmlspecialchars($targetUser['status']); ?>">
                        <?php if (isset($targetUser['root'])): ?>
                            <input type="hidden" name="root" value="<?php echo $targetUser['root']; ?>">
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="new_password" class="col-sm-4 control-label">New Password</label>
                        <div class="col-sm-8">
                            <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Leave blank to keep current">
                            <span class="help-block">Minimum 6 characters</span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password" class="col-sm-4 control-label">Confirm Password</label>
                        <div class="col-sm-8">
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm new password">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="col-sm-offset-4 col-sm-8">
                            <button type="submit" class="btn btn-warning">
                                <i class="fa fa-save"></i> Update Password
                            </button>
                            <a href="?_route=dashboard" class="btn btn-default">
                                <i class="fa fa-times"></i> Cancel
                            </a>
                        </div>
                    </div>
                </form>
                
                <hr>
                
                <div class="alert alert-info">
                    <h4><i class="fa fa-info-circle"></i> Password Security</h4>
                    <p>Passwords are securely hashed using bcrypt with a cost factor of <?php echo BCRYPT_COST; ?>.</p>
                </div>
            </div>
        </div>
    </div>
</div>
