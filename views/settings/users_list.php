<div class="row">
    <div class="col-md-12">
        <h1><i class="fa fa-users"></i> User Management</h1>
        <ol class="breadcrumb">
            <li><a href="?_route=dashboard">Dashboard</a></li>
            <li class="active">User Management</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-users"></i> All Users</h3>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>User Type</th>
                                <th>Status</th>
                                <th>Agent</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($users)): ?>
                                <?php foreach ($users as $userItem): ?>
                                    <tr>
                                        <td><?php echo $userItem['id']; ?></td>
                                        <td><?php echo htmlspecialchars($userItem['username']); ?></td>
                                        <td><?php echo htmlspecialchars($userItem['fullname']); ?></td>
                                        <td><?php echo htmlspecialchars($userItem['email'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($userItem['phone'] ?? ''); ?></td>
                                        <td>
                                            <span class="label label-<?php 
                                                echo match($userItem['user_type']) {
                                                    'SuperAdmin' => 'danger',
                                                    'Admin' => 'warning',
                                                    'Agent' => 'info',
                                                    'Sales' => 'success',
                                                    default => 'default'
                                                };
                                            ?>">
                                                <?php echo htmlspecialchars($userItem['user_type']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($userItem['status'] === 'active'): ?>
                                                <span class="label label-success">Active</span>
                                            <?php else: ?>
                                                <span class="label label-danger">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php 
                                            if ($userItem['root']) {
                                                foreach ($agents as $agent) {
                                                    if ($agent['id'] == $userItem['root']) {
                                                        echo htmlspecialchars($agent['fullname']);
                                                        break;
                                                    }
                                                }
                                            } else {
                                                echo '<span class="text-muted">None</span>';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <a href="?_route=settings/users-edit/<?php echo $userItem['id']; ?>" class="btn btn-sm btn-primary">
                                                <i class="fa fa-edit"></i> Edit
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center">No users found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
