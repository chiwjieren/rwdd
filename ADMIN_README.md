# GoGreen Admin CRUD System

## 🔐 Admin Login Credentials
- **Username/Email:** `admin`
- **Password:** `admin123`

## 📋 Setup Instructions

### 1. Database Setup
Run the SQL setup script to create all necessary tables:
```sql
-- Navigate to phpMyAdmin or your MySQL client
-- Select your database: rwdd_assignment
-- Run: src/sql/admin_system_setup.sql
```

Or run each table creation script individually:
- `create_event_table.sql`
- `create_newsletter_table.sql`
- `create_quiz_table.sql`
- `create_admin_log_table.sql`

### 2. Create Media Directory
Create the events media directory:
```
src/media/events/
```

### 3. Access Admin Dashboard
1. Go to your website login page
2. Enter admin credentials:
   - Email: `admin`
   - Password: `admin123`
3. You'll be automatically redirected to the admin dashboard

## 🎯 Admin Features

### Dashboard (`admin_dashboard.php`)
- View statistics for all system entities
- Monitor recent admin activity
- Quick access to all management sections

### Users Management (`admin_users.php`)
- ✅ **Create** new user accounts
- ✅ **Read/View** all registered users
- ✅ **Update** user information (name, email, password)
- ✅ **Delete** users (removes all associated data)

### Events Management (`admin_events.php`)
- ✅ **Create** new events with image upload
- ✅ **Read/View** all events
- ✅ **Update** event details (title, date, time, location, description, image)
- ✅ **Delete** events (removes associated images)

### Tips Management (`admin_tips.php`)
- ✅ **View** all user-submitted tips
- ✅ **Edit** tip title, content, and category
- ✅ **Delete** inappropriate or duplicate tips

### Marketplace Management (`admin_marketplace.php`)
- ✅ **View** all marketplace items with images
- ✅ **Edit** item names and descriptions
- ✅ **Delete** items (removes from marketplace and related swaps)

### Swap Requests Management (`admin_swaps.php`)
- ✅ **View** all swap requests with full details
- ✅ **Approve** pending swap requests
- ✅ **Reject** inappropriate swap requests
- ✅ **Delete** completed or cancelled swaps

### Newsletter Management (`admin_newsletter.php`)
- ✅ **View** all newsletter subscribers
- ✅ **Export** subscriber emails (copy to clipboard)
- ✅ **Delete/Unsubscribe** emails

### Quiz Questions Management (`admin_quiz.php`)
- ✅ **Create** new quiz questions with 4 options
- ✅ **Read/View** all quiz questions
- ✅ **Update** questions, options, correct answers, and categories
- ✅ **Delete** quiz questions

## 🎨 Design Features

### Professional Admin Interface
- **Sidebar Navigation** - Easy access to all sections
- **Green Theme** - Consistent with GoGreen branding
- **Responsive Design** - Works on desktop, tablet, and mobile
- **Statistics Cards** - Visual overview of system data
- **Action Logging** - Track all admin activities
- **Alert Messages** - Success/error feedback for all actions

### Security Features
- Session-based authentication
- Admin-only access (redirects non-admins)
- SQL injection prevention (prepared statements)
- File upload validation
- Confirmation dialogs for destructive actions

## 📁 File Structure

```
src/
├── main/
│   ├── admin_dashboard.php      # Main admin dashboard
│   ├── admin_users.php          # Users CRUD
│   ├── admin_events.php         # Events CRUD
│   ├── admin_tips.php           # Tips management
│   ├── admin_marketplace.php    # Marketplace management
│   ├── admin_swaps.php          # Swap requests management
│   ├── admin_newsletter.php     # Newsletter management
│   ├── admin_quiz.php           # Quiz questions CRUD
│   ├── admin_sidebar.php        # Reusable sidebar component
│   └── login.php               # Updated with admin check
├── css/
│   └── admin.css               # Admin panel styling
└── sql/
    ├── admin_system_setup.sql   # Complete setup script
    ├── create_event_table.sql
    ├── create_newsletter_table.sql
    ├── create_quiz_table.sql
    └── create_admin_log_table.sql
```

## 🔄 Admin Activity Logging

All admin actions are logged in the `ADMIN_LOG` table:
- Action type (CREATE, UPDATE, DELETE)
- Table affected
- Record ID
- Action details
- Timestamp

View logs in the Dashboard's "Recent Admin Activity" section.

## 🚀 Usage Tips

1. **Regular Users** - Login with regular email/password → Goes to index.php
2. **Admin** - Login with `admin`/`admin123` → Goes to admin dashboard
3. **Switch Views** - Admins can click "View Site" to see the public website
4. **Logout** - Click "Logout" in sidebar to end admin session

## ⚠️ Important Notes

- Admin credentials are hardcoded in `login.php`
- Change admin password in production environment
- Deleting users will delete all their items and swap requests
- Deleting items will delete related swap requests
- Uploaded images are stored in `../media/events/` and `../media/items/`
- Admin actions cannot be undone (except via database restore)

## 🎯 Future Enhancements (Optional)

- [ ] Multiple admin accounts
- [ ] Role-based permissions (super admin, moderator)
- [ ] Bulk delete operations
- [ ] Advanced filtering and search
- [ ] Export data to CSV
- [ ] Email notifications to users
- [ ] Google Calendar API integration for events
- [ ] Image editor for uploaded photos
- [ ] Analytics and charts

---

**Status:** ✅ All CRUD operations implemented and functional
**Last Updated:** October 24, 2025
