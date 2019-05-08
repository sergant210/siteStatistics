## siteStatistics

It's a MODX Extra for site statistics. It can be used to view:
- resource views and visits;
- users statistics;
- online users.

### Snippets
* siteStatistics
 
Displays information about views and visits of both individual resources and the entire site.
```html
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

### Backend
#### Tab "Users"
You can search only by a specific field. Format of search query - "scope:search query". For example, to filter rows by user fullname - `user:user fullname`.  
Available scopes: user, page, context, ip, user_agent, referer.  

To get only users with empty referer - `referef:`.
  

[Russian documentation](https://modzone.ru/documentation/sitestatistics/).