# ISP Billing Engine

Complete ISP billing portal with PHP, MySQL, Bootstrap 3. Features secure authentication, role-based permissions, plan management, and CSRF protection.

## Features

- User authentication with bcrypt hashing
- Role-based permissions (SuperAdmin, Admin, Agent, Sales)
- Plan management with access controls
- User profile editing
- CSRF protection on all forms
- Bootstrap 3 responsive UI

## Installation

- PHP 7.0+, MySQL/MariaDB, web server
- Clone project, import database, configure connection
- Access via web browser


2. **Create the database**:
   ```bash
   mysql -u root -p < database.sql
   ```

3. **Configure database connection** in `config/database.php`

4. **Access the application** via your web browser

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

## API Routes

- `/?_route=auth/login` - Login page
- `/?_route=auth/login-post` - Process login
- `/?_route=auth/logout` - Logout
- `/?_route=dashboard` - Main dashboard
- `/?_route=plan/list` - List plans
- `/?_route=plan/edit/{id}` - Edit plan
- `/?_route=plan/edit-post` - Process plan update
- `/?_route=settings/users-list` - List users
- `/?_route=settings/users-edit/{id}` - Edit user
- `/?_route=settings/users-edit-post` - Process user update

## License

MIT License - Feel free to use and modify.

### Setup Steps

1. **Clone/Download the project to your web server directory**

2. **Create the database**:
   ```bash
   mysql -u root -p < database.sql
   ```

3. **Configure database connection**:
   Edit `config/database.php` with your database credentials:
   ```php
   private $host = 'localhost';
   private $dbname = 'isp_billing';
   private $username = 'root';
   private $password = 'your_password';
   ```

4. **Seed the database with test data**:
   ```bash
   php seed_data.php
   ```

5. **Set proper permissions**:
   ```bash
   chmod -R 755 /path/to/ISP-Billing-Engine
   ```

6. **Access the application**:
   Open your browser and navigate to `http://localhost/ISP-Billing-Engine`

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

## File Structure

```
ISP-Billing-Engine/
├── config/
│   ├── database.php      # Database configuration
│   └── config.php         # Application constants
├── controllers/
│   ├── BaseController.php # Base controller with common methods
│   ├── AuthController.php # Authentication controller
│   ├── DashboardController.php # Dashboard controller
│   ├── PlanController.php   # Plan management controller
│   └── SettingsController.php # User management controller
├── helpers/
│   ├── AuthHelper.php     # Authentication helper
│   ├── CSRFHelper.php     # CSRF protection helper
│   └── PermissionHelper.php # Permission checking helper
├── views/
│   ├── layout/
│   │   ├── header.php     # HTML header and navigation
│   │   └── footer.php     # HTML footer
│   ├── auth/
│   │   └── login.php      # Login form
│   ├── dashboard/
│   │   └── index.php      # Dashboard page
│   ├── plan/
│   │   ├── list.php       # Plan listing
│   │   └── edit.php       # Plan editing form
│   └── settings/
│       └── edit_user.php  # User editing form
├── database.sql           # Database schema
├── seed_data.php          # Test data seeder
├── index.php              # Main entry point and routing
└── README.md              # This file
```

## Testing

To test the permission system:

1. **Test SuperAdmin access**:
   - Login as `superadmin/admin123`
   - Try to edit any plan (should work)
   - Try to edit any user (should work)

2. **Test Admin access**:
   - Login as `admin/admin123`
   - Try to edit any plan (should work)
   - Try to edit SuperAdmin user (should be blocked)

3. **Test Agent access**:
   - Login as `agent1/agent123` or `agent2/agent123`
   - Try to edit enabled plan (should work)
   - Try to edit disabled plan (should be blocked)
   - Try to edit Sales users under their tree (should work)
   - Try to edit Sales users under other agents (should be blocked)
   - Try to edit their own profile (should work)

4. **Test Sales access**:
   - Login as `sales1/sales123` (under agent1)
   - Try to edit enabled plan with recharge (should work)
   - Try to edit disabled plan (should be blocked)
   - Login as `sales2/sales123` (under agent1)
   - Should see sales1's recharges (same agent tree)
   - Login as `sales3/sales123` (under agent2)
   - Should only see their own recharges (different agent tree)

5. **Test Tree-Based Permissions**:
   - Sales1 and Sales2 can see each other's recharges (same agent tree)
   - Sales3 cannot see Sales1/Sales2 recharges (different agent tree)
   - Agent1 can edit Sales1 and Sales2 (their tree)
   - Agent2 can edit Sales3 (their tree)
   - Agents cannot edit other agents

## Reference Information (Previously Shown in UI)

### Test Credentials Panel (From Login Page)
```
SuperAdmin: superadmin / admin123
Admin: admin / admin123
Agent1: agent1 / agent123
Agent2: agent2 / agent123
Sales1: sales1 / sales123 (under agent1)
Sales2: sales2 / sales123 (under agent1)
Sales3: sales3 / sales123 (under agent2)
```

### Permission Information Panel (From Plan List Page)

#### Who can edit plans:
- **SuperAdmin & Admin**: Can edit any plan
- **Agent**: Can edit only enabled plans
- **Sales**: Can edit only enabled plans they have recharges for (within their agent tree)

#### Who can edit users:
- **SuperAdmin**: Can edit any user
- **Admin**: Can edit Admin, Agent, and Sales users (but not SuperAdmin)
- **Agent**: Can edit Sales users under their tree and themselves
- **Sales**: Can only edit themselves

#### Tree Structure:
- **Agent1** manages Sales1 and Sales2
- **Agent2** manages Sales3
- **Sales users** can see recharges from users in the same agent tree
- **Sales users** cannot see recharges from users in different agent trees

#### Plan Status:
- **Enabled** - Plan is active and can be used
- **Disabled** - Plan is inactive and cannot be used by Agent/Sales users

### User Management Information Panel (From Users List Page)

#### Who can edit users:
- **SuperAdmin**: Can edit any user including other SuperAdmins
- **Admin**: Can edit Admin, Agent, and Sales users (but not SuperAdmin)
- **Agent**: Can edit Sales users under their tree and themselves
- **Sales**: Can only edit themselves

#### User Hierarchy:
- **SuperAdmin** > **Admin** > **Agent** > **Sales**
- **Agents** manage teams of Sales users
- **Sales users** are assigned to Agents via the `root` field
- **Cross-tree access is blocked** (Agent1 cannot edit Agent2's Sales users)

### Plan Information Panel (From Plan Edit Page)

#### Role-based Restrictions:
- **SuperAdmin & Admin**: No restrictions on plan editing
- **Agent**: Can only edit enabled plans
- **Sales**: Can only edit enabled plans within their user tree

#### Plan Status Indicators:
- **Enabled** (Green label) - Plan is active and editable by Agent/Sales
- **Disabled** (Red label) - Plan is inactive, only editable by SuperAdmin/Admin

## License

This project is for demonstration purposes only.
