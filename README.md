# ISP Billing Engine

 ISP billing portal with PHP, MySQL and Bootstrap 3. Features include secure authentication, role-based permissions, plan management and CSRF protection.

## Features

- User authentication with bcrypt hashing
- Role-based permissions (SuperAdmin, Admin, Agent, Sales)
- Plan management with access controls
- User profile editing
- CSRF protection on all forms
- Bootstrap 3 responsive UI

  
## Database Schema

The application uses the following tables:

- **tbl_users**: User accounts with role-based permissions
- **tbl_plans**: ISP service plans
- **tbl_user_recharges**: Customer recharge records
- **tbl_voucher**: Voucher management

## Security Features

- **CSRF Protection**: All forms protected with token validation
- **Input Validation**: Sanitization and validation of user inputs
- **Password Hashing**: Secure bcrypt password storage
- **Session Management**: Secure session handling
- **Role-Based Access**: Hierarchical permission system

## User Roles and Permissions

| Role | Access Level | Permissions |
|-------|-------------|------------|
| SuperAdmin | Full | Complete system access |
| Admin | High | User and plan management |
| Agent | Medium | Limited plan access, user management within tree |
| Sales | Basic | Self-service and assigned plan access |

## Default Login Credentials

| Role | Username | Password |
|------|----------|----------|
| SuperAdmin | superadmin | admin123 |
| Admin | admin | admin123 |
| Agent1 | agent1 | agent123 |
| Agent2 | agent2 | agent123 |
| Sales1 | sales1 | sales123 |
| Sales2 | sales2 | sales123 |
| Sales3 | sales3 | sales123 |

## URL Routing

The application uses the following URL structure:

- `/?_route=dashboard` - Dashboard (default)
- `/?_route=auth/login` - Login page
- `/?_route=plan/list` - List all plans
- `/?_route=plan/edit/{id}` - Edit plan
- `/?_route=plan/edit-post` - Process plan edit
- `/?_route=settings/users-edit/{id}` - Edit user
- `/?_route=settings/users-edit-post` - Process user edit
- `/?_route=auth/logout` - Logout

## Permission System

### Plan Edit Permissions

- **SuperAdmin & Admin**: Can edit any plan
- **Agent**: Can only edit enabled plans
- **Sales**: Can only edit enabled plans within their user tree

### User Edit Permissions

- **SuperAdmin**: Can edit any user
- **Admin**: Can edit Admin, Agent, and Sales users (not SuperAdmin)
- **Agent**: Can edit Sales users under their tree
- **Sales**: Can only edit themselves

## Security Features

- **CSRF Protection**: All forms include CSRF tokens
- **Password Hashing**: Uses bcrypt with cost factor 12
- **Input Validation**: Server-side validation for all inputs
- **SQL Injection Prevention**: Uses prepared statements
- **XSS Prevention**: Output escaping with htmlspecialchars
- **Session Security**: Secure session configuration

## Database Schema

The application uses the following tables:

- `tbl_users` - User accounts and authentication
- `tbl_plans` - ISP plans and pricing
- `tbl_user_recharges` - User recharge records
- `tbl_voucher` - Voucher management




