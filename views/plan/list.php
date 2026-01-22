<div class="row">
    <div class="col-md-12">
        <h1><i class="fa fa-list"></i> Plans</h1>
        <ol class="breadcrumb">
            <li><a href="?_route=dashboard">Dashboard</a></li>
            <li class="active">Plans</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-list"></i> All Plans</h3>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Plan Name</th>
                                <th>Price</th>
                                <th>Type</th>
                                <th>RADIUS</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($plans)): ?>
                                <?php foreach ($plans as $plan): ?>
                                    <tr>
                                        <td><?php echo $plan['id']; ?></td>
                                        <td><?php echo htmlspecialchars($plan['name_plan']); ?></td>
                                        <td>R<?php echo number_format($plan['price'], 2); ?></td>
                                        <td>
                                            <span class="label label-<?php echo $plan['type'] === 'prepaid' ? 'success' : 'info'; ?>">
                                                <?php echo ucfirst($plan['type']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($plan['is_radius']): ?>
                                                <span class="label label-warning">RADIUS</span>
                                            <?php else: ?>
                                                <span class="label label-default">No</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($plan['enabled']): ?>
                                                <span class="label label-success">Enabled</span>
                                            <?php else: ?>
                                                <span class="label label-danger">Disabled</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="?_route=plan/edit/<?php echo $plan['id']; ?>" class="btn btn-sm btn-primary">
                                                <i class="fa fa-edit"></i> Edit
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">No plans found</td>
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
