# Operations and data stewardship

This checklist is for the barangay administrator and whoever maintains the Wasmer deployment. It avoids setting legal or records-management periods on their behalf.

## Backups and restore checks

- Keep the database backup separate from the production database and restrict access to named maintainers.
- Back up the persistent `/data` volume, which contains private report photos and any retained verification files. The app's `/up` health endpoint does not test backup availability.
- Confirm with the actual database and hosting providers how database and volume backups are created, encrypted, retained, and restored. The repository does not contain credentials or an automated provider backup integration.
- Record the date, operator, covered database/volume, and result for every backup and restore check. Test restoring to a private staging environment before relying on a backup.
- Before a deployment that changes the schema or stored files, take a fresh backup and verify that rollback/restore steps are available.

## Approve a retention schedule before enabling deletion

The app currently does not automatically delete reports, curfew records, or audit history. The barangay should approve and document the periods below with its records/privacy adviser before a purge job is added.

| Data | Retention period | Approval / review date |
| --- | --- | --- |
| Incident reports and attachments | To be approved | To be recorded |
| Curfew records | To be approved | To be recorded |
| Audit logs | To be approved | To be recorded |
| Database and file backups | To be approved with provider | To be recorded |
| Rejected/verified residency request metadata | To be approved | To be recorded |

Verification proof images are removed after a decision by the application. Avoid keeping separate copies unless the barangay has an approved purpose and retention schedule. Do not delete audit records ad hoc: staff may need them for accountability, while excessive retention creates privacy risk.

## Live service monitoring

The app exposes `/up` for availability checks. It also returns a `Server-Timing` header and logs slow requests with route name, HTTP method, status, and duration only. A random 10% sample of main-interface visits sends browser timing measurements to the app logs using a broad page group, without account IDs, URLs, or form contents. Set `SLOW_REQUEST_THRESHOLD_MS` to tune the warning threshold (default 1500 ms). Enable GitHub Actions failure notifications so maintainers see uptime and mobile audit failures.
