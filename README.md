## siteStatistics

It's a MODX Extra for site statistics. It can be used to view:
- resource views and visits;
- users statistics;
- online users.

### Snippets
* siteStatistics
 
Displays information about views and visits of both individual resources and the entire site.
```$html
<!-- Current resource views -->
[[!siteStatistics]]

<!-- Current resource visits -->
[[!siteStatistics? &show=`users`]]
```
* siteOnlineUsers

Displays information about users who are currently on the site.
```html
<!-- Short mode -->
[[!siteOnlineUsers]]
<!-- Detailed list of users -->
[[!siteOnlineUsers? &fullMode=`1`]]
```