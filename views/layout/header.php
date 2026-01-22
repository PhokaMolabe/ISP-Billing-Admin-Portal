<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?></title>
    
    <!-- Bootstrap 3 CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    
    <style>
        .navbar-brand {
            font-weight: bold;
        }
        .panel-heading {
            font-weight: bold;
        }
        .form-horizontal .control-label {
            text-align: right;
        }
        .footer {
            margin-top: 50px;
            padding: 20px 0;
            border-top: 1px solid #eee;
            text-align: center;
            color: #777;
        }
    </style>
</head>
<body>
    <?php if (isset($user) && $user): ?>
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="?_route=dashboard"><?php echo APP_NAME; ?></a>
            </div>
            
            <div id="navbar" class="navbar-collapse collapse">
                <ul class="nav navbar-nav">
                    <li><a href="?_route=dashboard"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button">
                            <i class="fa fa-list"></i> Plans <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="?_route=plan/list">Plan List</a></li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button">
                            <i class="fa fa-cog"></i> Settings <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <?php if ($user['user_type'] === USER_TYPE_SUPERADMIN || $user['user_type'] === USER_TYPE_ADMIN || $user['user_type'] === USER_TYPE_AGENT): ?>
                                <li><a href="?_route=settings/users-list"><i class="fa fa-users"></i> User Management</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                </ul>
                
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button">
                            <i class="fa fa-user"></i> <?php echo htmlspecialchars($user['fullname']); ?> <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="?_route=settings/users-edit/<?php echo $user['id']; ?>"><i class="fa fa-edit"></i> Edit Profile</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="?_route=auth/logout"><i class="fa fa-sign-out"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <?php endif; ?>
    
    <div class="container-fluid">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                <?php 
                    echo htmlspecialchars($_SESSION['success']); 
                    unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                <?php 
                    echo htmlspecialchars($_SESSION['error']); 
                    unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>
