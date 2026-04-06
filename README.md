# Hvar Excursions Premium

Custom WordPress code for the Hvar Excursions project.

This repository stores only the custom project code:

- `wp-content/themes/catamaran-child`
- `wp-content/plugins/hvar-bookings`
- `wp-content/mu-plugins`

It does not store WordPress core, uploads, database dumps, or local secrets.

## Local Development

Local WordPress site:

- `C:\laragon\www\hvar-excursions`
- `http://hvar-excursions.test:8080/`

Laragon uses this repository directly through Windows junctions:

- `C:\laragon\www\hvar-excursions\wp-content\themes\catamaran-child`
  -> `C:\Users\Ivan\source\repos\hvar-excursions-premium\wp-content\themes\catamaran-child`
- `C:\laragon\www\hvar-excursions\wp-content\plugins\hvar-bookings`
  -> `C:\Users\Ivan\source\repos\hvar-excursions-premium\wp-content\plugins\hvar-bookings`
- `C:\laragon\www\hvar-excursions\wp-content\mu-plugins`
  -> `C:\Users\Ivan\source\repos\hvar-excursions-premium\wp-content\mu-plugins`

Because of that, edits in this repo are reflected immediately in the local site.

## Daily Workflow

1. Edit code in this repository.
2. Test locally on Laragon.
3. Commit and push to GitHub.
4. Deploy the changed folders to staging.
5. Verify on staging before changing production.

## Staging

Staging site:

- `https://staging.hvarexcursions.com/`

Staging should have:

- parent theme `catamaran` installed separately
- child theme from this repo uploaded to `wp-content/themes/catamaran-child`
- plugin from this repo uploaded to `wp-content/plugins/hvar-bookings`
- MU plugins from this repo uploaded to `wp-content/mu-plugins`

After code or database changes on staging:

1. Make sure `Catamaran Child Theme` is active.
2. Make sure `Hvar Bookings` is active.
3. Save permalinks in WordPress admin.
4. Test:
   - `/`
   - `/rentals/`
   - `/excursions/`
   - `/transfers/`
   - `/contacts/`
   - `/internal-bookings/`
   - `/internal-dispatch/`

## Notes

- SMTP secrets are not stored in this repo.
- The local SMTP secret file stays outside the repo.
- Parent theme updates or third-party plugin updates should not be committed here unless intentionally needed.
