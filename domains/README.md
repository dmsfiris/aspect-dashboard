# Domains Folder

This folder contains PHP files related to managing domains within the Aspect Dashboard application.

## Files

- `manage-domains.php`  
  The PHP script responsible for the domain management interface, including listing, adding, editing, or deleting domains via interaction with the backend API or data source.

## Notes

- Domain data management is handled through the API server and JSON files located elsewhere (`node-api/domains/domains.json`).
- Ensure `manage-domains.php` is kept updated to match the API endpoints and data format.
- Use this folder for PHP scripts and resources strictly related to domain management UI and server-side logic.
