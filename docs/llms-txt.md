# llms.txt

A static [llms.txt](https://llmstxt.org) file served at the site root
(`https://simonecerruti.com/llms.txt`) so AI agents and LLM-powered tools can
understand the site without crawling every page.

## What it is

A hand-written, committed Markdown file at `public/llms.txt`, served directly by
the web server like `public/robots.txt`. It is **not** generated — unlike
`public/sitemap.xml`, which is produced by the `app:generate-sitemap` command and
is git-ignored.

## Contents

- Identity & positioning: technical partner building bespoke / tailor-made software.
- All services: management software (gestionali), ERP, CRM, web platforms, SaaS,
  e-commerce, websites, PWA, automation, AI-powered tools.
- Local-SEO signal (Biella, Piedmont, Italy) framed around remote, worldwide work.
- Fully bilingual (EN + IT) page links.
- All contacts and social profiles.

## Maintenance

Because the file is static, update `public/llms.txt` by hand when contacts,
services or positioning change. `tests/Feature/LlmsTxtTest.php` guards the file
and keeps the contacts and social links in sync with `config/company.php`: change
a contact there and the test fails until `llms.txt` is updated to match.

## Notes

- Excluded from analytics tracking via `skip_paths` in `config/analytics.php`,
  alongside `robots.txt` and `sitemap*.xml`.
