INSERT INTO banner.banner (id, url, k, view_count, total_views) VALUES (1, 'https://cdn.some-site.com/01-banner.jpg', 1, 0, 1000);
INSERT INTO banner.banner (id, url, k, view_count, total_views) VALUES (2, 'https://cdn.some-site.com/02-banner.jpg', 1, 0, 10000);
INSERT INTO banner.banner (id, url, k, view_count, total_views) VALUES (3, 'https://cdn.some-site.com/03-banner.jpg', 1, 0, 5000);
INSERT INTO banner.banner (id, url, k, view_count, total_views) VALUES (4, 'https://cdn.some-site.com/04-banner.jpg', 1, 0, 15000);
INSERT INTO banner.banner (id, url, k, view_count, total_views) VALUES (5, 'https://cdn.some-site.com/05-banner.jpg', 1, 0, 8000);

-- calculate view k
UPDATE banner
SET banner.k = (SELECT max_total FROM (SELECT MAX(total_views) AS max_total FROM banner) as m) / banner.total_views;
