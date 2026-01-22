<div class="row">
    <div class="col-md-12">
        <h1><i class="fa fa-dashboard"></i> Dashboard</h1>
        <p class="text-muted">Welcome back, <?php echo htmlspecialchars($user['fullname']); ?>!</p>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-users"></i> Total Users</h3>
            </div>
            <div class="panel-body text-center">
                <h2><?php echo number_format($stats['total_users']); ?></h2>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="panel panel-success">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-list"></i> Total Plans</h3>
            </div>
            <div class="panel-body text-center">
                <h2><?php echo number_format($stats['total_plans']); ?></h2>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-refresh"></i> Active Recharges</h3>
            </div>
            <div class="panel-body text-center">
                <h2><?php echo number_format($stats['active_recharges']); ?></h2>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="panel panel-warning">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-ticket"></i> Total Vouchers</h3>
            </div>
            <div class="panel-body text-center">
                <h2><?php echo number_format($stats['total_vouchers']); ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-info-circle"></i> User Information</h3>
            </div>
            <div class="panel-body">
                <table class="table table-striped">
                    <tr>
                        <td><strong>Username:</strong></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Full Name:</strong></td>
                        <td><?php echo htmlspecialchars($user['fullname']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>User Type:</strong></td>
                        <td><span class="label label-primary"><?php echo htmlspecialchars($user['user_type']); ?></span></td>
                    </tr>
                    <tr>
                        <td><strong>User ID:</strong></td>
                        <td><?php echo $user['id']; ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-rocket"></i> Quick Actions</h3>
            </div>
            <div class="panel-body">
                <div class="list-group">
                    <a href="?_route=plan/list" class="list-group-item">
                        <i class="fa fa-list"></i> View All Plans
                    </a>
                    <a href="?_route=settings/users-edit/<?php echo $user['id']; ?>" class="list-group-item">
                        <i class="fa fa-edit"></i> Edit My Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
