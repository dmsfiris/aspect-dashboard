# Stores Data Folder

This folder contains the `stores.json` file, which stores the list of stores used by the application.

## File Description

- `stores.json`  
  A JSON array where each object represents a store with the following properties:  
  - `id` (number): Unique identifier for the store  
  - `name` (string): Store name  
  - `owner` (string): Store owner name  
  - `contact_email` (string): Contact email for the store  

## Notes

- This file is read and updated by the Node.js API server (`node-api/index.js`).
- Avoid manual edits while the server is running to prevent data inconsistencies.
- Access to `.json` files may be restricted by `.htaccess` or server configuration for security.
- Backup this file regularly to avoid accidental data loss.

## Usage

The Node.js API exposes CRUD endpoints to manage stores, which read/write this file directly.

