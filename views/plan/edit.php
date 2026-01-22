<div class="row">
    <div class="col-md-12">
        <h1><i class="fa fa-edit"></i> Edit Plan</h1>
        <ol class="breadcrumb">
            <li><a href="?_route=dashboard">Dashboard</a></li>
            <li><a href="?_route=plan/list">Plans</a></li>
            <li class="active">Edit Plan</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-edit"></i> Edit Plan Details</h3>
            </div>
            <div class="panel-body">
                <?php if (isset($errors) && $errors): ?>
                    <div class="alert alert-danger">
                        <ul class="list-unstyled">
                            <?php foreach ($errors as $field => $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form class="form-horizontal" method="POST" action="?_route=plan/edit-post/<?php echo $plan['id']; ?>">
                    <?php echo $csrf_field; ?>
                    
                    <div class="form-group">
                        <label for="id" class="col-sm-3 control-label">Plan ID</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="id" value="<?php echo $plan['id']; ?>" readonly>
                            <span class="help-block">Plan ID cannot be changed</span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="name_plan" class="col-sm-3 control-label">Plan Name *</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="name_plan" name="name_plan" 
                                   value="<?php echo htmlspecialchars($plan['name_plan']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="price" class="col-sm-3 control-label">Price (R) *</label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <span class="input-group-addon">R</span>
                                <input type="number" class="form-control" id="price" name="price" 
                                       value="<?php echo htmlspecialchars($plan['price']); ?>" 
                                       step="0.01" min="0" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="type" class="col-sm-3 control-label">Plan Type *</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="type" name="type" required>
                                <option value="prepaid" <?php echo $plan['type'] === 'prepaid' ? 'selected' : ''; ?>>
                                    Prepaid
                                </option>
                                <option value="postpaid" <?php echo $plan['type'] === 'postpaid' ? 'selected' : ''; ?>>
                                    Postpaid
                                </option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="is_radius" class="col-sm-3 control-label">RADIUS Plan</label>
                        <div class="col-sm-9">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="is_radius" value="1" 
                                           <?php echo $plan['is_radius'] ? 'checked' : ''; ?>>
                                    This is a RADIUS-based plan
                                </label>
                            </div>
                            <span class="help-block">RADIUS plans require RADIUS authentication</span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="enabled" class="col-sm-3 control-label">Status</label>
                        <div class="col-sm-9">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="enabled" value="1" 
                                           <?php echo $plan['enabled'] ? 'checked' : ''; ?>>
                                    Plan is enabled
                                </label>
                            </div>
                            <span class="help-block">
                                <?php if ($user['user_type'] === USER_TYPE_SUPERADMIN || $user['user_type'] === USER_TYPE_ADMIN): ?>
                                    Disabling a plan will prevent Agent and Sales users from editing it
                                <?php else: ?>
                                    Only SuperAdmin and Admin can change plan status
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-9">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Save Changes
                            </button>
                            <a href="?_route=plan/list" class="btn btn-default">
                                <i class="fa fa-times"></i> Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Plan Recharges Panel -->
        <div class="panel panel-info">
            <div class="panel-heading">
                <h4 class="panel-title"><i class="fa fa-history"></i> Recent Recharges (Last 10)</h4>
            </div>
            <div class="panel-body">
                <?php if (!empty($recharges)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Status</th>
                                    <th>Recharged On</th>
                                    <th>Recharged Time</th>
                                    <th>Expiration</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recharges as $recharge): ?>
                                    <tr>
                                        <td>
                                            <?php if ($recharge['customer_name']): ?>
                                                <?php echo htmlspecialchars($recharge['customer_name']); ?>
                                                <br><small class="text-muted"><?php echo htmlspecialchars($recharge['customer_username']); ?></small>
                                            <?php else: ?>
                                                <span class="text-muted">Customer ID: <?php echo $recharge['customer_id']; ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($recharge['status'] === 'active'): ?>
                                                <span class="label label-success">Active</span>
                                            <?php elseif ($recharge['status'] === 'inactive'): ?>
                                                <span class="label label-warning">Inactive</span>
                                            <?php else: ?>
                                                <span class="label label-danger">Expired</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($recharge['recharged_on']); ?></td>
                                        <td><?php echo htmlspecialchars($recharge['recharged_time']); ?></td>
                                        <td><?php echo htmlspecialchars($recharge['expiration'] ?? 'Not set'); ?></td>
                                        <td><?php echo htmlspecialchars($recharge['time'] ?? 'Not set'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> No recharges found for this plan.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
