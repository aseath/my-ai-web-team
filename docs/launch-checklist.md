# Launch Checklist

## UK Legal & Compliance
- [ ] Privacy notice reviewed by the client (UK GDPR): what is collected, why, how long, who it's shared with, rights, ICO complaint route
- [ ] Cookie notice. Consent banner required before any non-essential cookies/analytics are set (PECR). None needed if only strictly necessary cookies are used (session/CSRF)
- [ ] Limited company: registered name, number, registered office address and place of registration on the site (Companies Act)
- [ ] Accessibility statement (recommended; required for public-sector clients)
- [ ] Client has confirmed every testimonial, statistic and accreditation is real and usable
- [ ] Client's ICO registration (data protection fee) confirmed if they process personal data

## Third-party Services & CSP
| Service | CSP directive & origin added | Sets cookies? | Consent needed? |
|---|---|---|---|
| | | | |

## Content
- [ ] No `[COPY NEEDED]`, `[NEEDS CLIENT INPUT]` or lorem ipsum anywhere (`grep -rn "NEEDS CLIENT\|COPY NEEDED\|lorem" src/`)
- [ ] Client has signed off copy and images
- [ ] Favicon, apple-touch-icon and OG image (1200×630) in place
- [ ] Custom 404 page checked

## SEO
- [ ] Unique title and description on every page
- [ ] Canonical URLs use the production domain
- [ ] `sitemap.xml` lists all pages with the production domain; `robots.txt` references it
- [ ] Structured data checked with Google's Rich Results Test
- [ ] Redirects from the old site's URLs (if replacing a site)
- [ ] Google Search Console and Business Profile set up / updated

## Performance & Quality
- [ ] Lighthouse mobile: Performance ≥ 90, Accessibility ≥ 95, Best Practices ≥ 95, SEO ≥ 95
- [ ] Images compressed, modern formats, explicit dimensions
- [ ] qa-reviewer verdict PASS on the final build
- [ ] Forms tested end to end on production (email actually arrives)

## Production Configuration
- [ ] `APP_ENV=production` (errors hidden, logged)
- [ ] HTTPS enforced, HSTS header added
- [ ] Real mail transport configured (not `LogMailer`)
- [ ] Strong unique DB credentials; DB not publicly exposed
- [ ] Backups of DB and uploads scheduled
- [ ] Uptime monitoring on `/health`
