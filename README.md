# AC nette project

This is a application built using [Nette](https://nette.org).

## Requirements

This Web Project is compatible with Nette 3.2 and requires PHP 8.2.

## Installation

You need [Composer](https://getcomposer.org/) and [npm](https://nodejs.org/en/learn/getting-started/an-introduction-to-the-npm-package-manager) or `yarn` or `pnpm`.

Copy project in your path:

    git clone https://github.com/i-jurij/AC_nette.git
    cd AC_nette
    composer install
    npm install
    npm run build

Ensure the `temp/` and `log/` directories are writable.

## Web Server Setup

To quickly dive in, use PHP's built-in server:

    php -S localhost:8000 -t www

Then, open `http://localhost:8000` in your browser to view the welcome page.

For Apache or Nginx users, configure a virtual host pointing to your project's `www/` directory.

**Important Note:** Ensure `app/`, `config/`, `log/`, and `temp/` directories are not web-accessible.
Refer to [security warning](https://nette.org/security-warning) for more details.

## CSS

For admins panel app use CSS framework [bootstrap](https://getbootstrap.com/).  
For other pages app use [oswc2_styles](https://github.com/i-jurij/oswc2_styles) - collected from [BassCSS](https://basscss.com/) and [Picnic](https://picnicss.com/) using [Rollup](https://rollupjs.org/) and [rollup-plugin-css-porter](https://www.npmjs.com/package/rollup-plugin-css-porter).  
!!! In BassCSS renamed ".flex" to ".flexx".  
BassCSS using for functionality (typo, margin, padding, display ... ).  
Picnic using for components (buttons, forms, cards, blocks ...).

## Webpack

    `npm run dev` - mode development (rereads js and css on the fly, address is host_ip:3000/assets),
    `npm run build` - mode production for prepare js and css into www/assets.

Entry is `resources/js/app.js` and `resources/css/style.css`, all other js files and css files can be imported into them.

## Work

### Start

Create database with name "AC" from console or phpmyadmin eg.
Then run console command into project folder:  
`php ./bin/start.php migrate`  
for created db tables. It safely for data in existing tables.  
SQL query for tables created is in file "create_sql.php".
Then if you need you can added category and services manually from dump file of database  
AC_start_need_create_admin_user_from_console.sql

Then run:  
`php ./bin/start.php useradd <username> <password>`  
for user with admin grants creating.  
Password minimal length = 7, it can be change in config.

**_After user with specific permissions adding you must first permissions added_**  
Other users can be added from the admin panel.

Before user creating you can create permissions (resource, action) then roles.

1. Create PERMISSIONS for user
2. Create ROLES with permissions for user
3. Create USER

Permissions can be added automatically from classes names of models directory and their methods (you can change it from presenters and theirs methods) or manually.  
If you set in permissions only resource without action - you allow all actions of the resource.

**_If you change columns of table "users" in file "create_sql.php" change it in "app/Model/UsersTableColumns.php" too_**

**_Users factory can be run from "bin/factorys/user"_**

### Config

Configs files are located in "app/config". Read this [documentation](https://doc.nette.org/en/configuring).  
Change configs as you need: site name, db name, passwords and other things.

### Routing

"app/Core/RouterFactory.php", [documentation](https://doc.nette.org/en/application/routing)  
Application use three main routes: "Home", "Sign" and "Admin".  
Other routes as in Nette Framework: "Home::Pages::OtherPresenters::method", "Admin::Pages::OtherPresenters::method".

### Models

`RoleFacade` - get data from table pages (for menu at main page).  
`PermissionFacade` - add, edit, delete permissions  
`UserFacade` - add, edit, delete users

#### DB

See directory "bin/mysql".

### Accessory

Traits and classes for using in presenters.  
`RequireLoggedUser` - trait for admins pages that need autentication (in user not logged - redirect to sign in)
`RequireLoggedClient` - trait for home pages that need autentication (in client not logged - redirect to sign in)

### Authentication

Use Nette\Security\User with my simple App\Core\Authenticator with Nette Coockie Storage, but  
in App\Bootstrap.php logic for set storage and table into db was added

### Authorization

ACL  
roles, permissions (resource, action)
user -> array roles  
role -> array permissions  
permission is resource and action  
App\Core\MyAuthorizator check permissions.

#### Client banned

Admin - Clients - Add roles - banned. If client want autorize he wiil be redirect to default errrors page.

### Admin page and menu creating

#### Nav menu

For admins basic pages: "Users", "Roles", "Permissions", "Logs", "Cache".  
It is created manually.

#### Sidebar menu

Menu section CMS is created automatically from filesystem.  
The file structure is as follows eg:

```
CMS dir
	First dir
	FirstPresenter.php (namespace "App\UI\Cms\First" and class "FirstPresenter")
			Second dir
			SecondPresenter.php (namespace "App\UI\Cms\Second" and class "SecondPresenter")
					Third dir
					ThirdPresenter.php (namespace "App\UI\Cms\Third" and class "ThirdPresenter")
```

Menu point will be viewed only if Presenters class existed.
That is, only controllers classes are used and if you not need the menu point - create method into presenter. And if you need menu links - create dirs and into it create presenters class for action.  
Permissions for user is ["Cms", "menu"].

## Admins Basic pages

### Admins nav menu

#### Users

Add, edit, delete users and their roles.

#### Roles

Add, edit, delete roles and permissions for roles.

#### Permissions

Add, edit, delete Permissions. Permissions can be get from Models or Presenters classes (or both: see `PermissionFacade`) as their methods.

#### Cache

Clear cache or delete different file(s)

#### Logs

List, show, clear logs

## Admins additional pages and sidebar menu

#### CMS

SEO --- MAKE IT!!!  
Create, update, delete clients (customers, executors)  
Create, update, delete offers, comments, rating

## Front (home)

### Offers

#### Comments

The number of comments in one offer is limited to 100.  
This can be change into `\App\UI\Model\CommentFacade.php`

#### Chat

Чат между клиентами:  
кнопка возле профиля показывает общее количество новых сообщений в чате для этого пользователя  
и открывает список комнат чата, где можно выбрать конкретную.  
На странице объявления кнопка после телефона показывает количество новых сообщений от владельца объявления  
и открывает комнату чата этого объявления.

#### Grievance

Жалобы на объявления или комментарии

## DB

Mariadb 10.4 - 10.10  
В случае создания БД не из дампа, а с нуля:

TEST data for DB Only for testing  
`php bin/factorys/client create 10  
php bin/factorys/service create  
php bin/factorys/offers create 25`
