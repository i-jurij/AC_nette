<?php

$role = 'CREATE TABLE IF NOT EXISTS "role" 
	(	"id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 
		"role_name" TEXT UNIQUE  ON CONFLICT ROLLBACK NOT NULL
	)';
$permission = 'CREATE TABLE IF NOT EXISTS "permission" 
	(	"id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
		"resource" TEXT NOT NULL, 
		"action" TEXT DEFAULT NULL
	)';
$role_permission = 'CREATE TABLE IF NOT EXISTS "role_permission" 
	(	"role_id" INTEGER NOT NULL, 
		"permission_id" INTEGER NOT NULL,
		PRIMARY KEY ("role_id", "permission_id")
	)';
$user = 'CREATE TABLE IF NOT EXISTS "user" 
	(	"id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 
		"username" TEXT NOT NULL UNIQUE ON CONFLICT ROLLBACK, 
		"image" TEXT,
		"password" TEXT NOT NULL, 
		"phone" TEXT UNIQUE DEFAULT null, 
		"phone_verified" INTEGER DEFAULT 0,
		"email" TEXT UNIQUE DEFAULT null, 
		"email_verified" INTEGER DEFAULT 0, 
		"auth_token" TEXT, 
		"created_at" TEXT NOT NULL DEFAULT current_timestamp,
		"updated_at" TEXT NOT NULL DEFAULT current_timestamp
	)';
$role_user = 'CREATE TABLE IF NOT EXISTS "role_user" 
	(	
		"role_id" INTEGER NOT NULL, 
		"user_id" INTEGER NOT NULL,
		PRIMARY KEY ("role_id", "user_id")
	)';
$client = 'CREATE TABLE IF NOT EXISTS "client" 
	(	"id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 
		"name" TEXT, 
		"image" TEXT,
		"phone" TEXT NOT NULL UNIQUE, 
		"phone_verified" INTEGER DEFAULT 0,
		"email" TEXT UNIQUE DEFAULT null, 
		"email_verified" INTEGER DEFAULT 0,
		"auth_token" TEXT, 
		"created_at" TEXT NOT NULL DEFAULT current_timestamp,
		"updated_at" TEXT NOT NULL DEFAULT current_timestamp
	)';

$page = 'CREATE TABLE IF NOT EXISTS page
	(	"id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 
		"alias" VARCHAR(100) UNIQUE  ON CONFLICT ROLLBACK NOT NULL,
		"route" VARCHAR(100),
		"path" VARCHAR(100), 
		"title" VARCHAR(100), 
		"description" VARCHAR(255), 
		"keywords" VARCHAR(500), 
		"robots" VARCHAR(100), 
		"img" TEXT, 
		"content" TEXT, 
		"published" INTEGER,
		"access_level" VARCHAR(10), 
		"admin" INTEGER,
		"created_at" TEXT NOT NULL DEFAULT current_timestamp,
		"updated_at" TEXT NOT NULL DEFAULT current_timestamp
	)';

$create_sqls = [
    'role' => $role,
    'permission' => $permission,
    'role_permission' => $role_permission,
    'user' => $user,
    'role_user' => $role_user,
    'page' => $page,
    'client' => $client,
];
