
# newsletter2

**newsletter2** is a **complete reimplementation**  of the newsletter module for UliCMS. It runs on **UliCMS 2018.3.3 or later**  and it is **GDPR compliant**  . newsletter is **backwards compatible**  to the old newsletter modul and may be used as a drop-in replacement

## Features

[...]

## System Requirements

* UliCMS 2018.3.3 or later

* mail_queue module (optional) (recommend)

## Installation instructions

If you don’t migrate from the old newsletter module to **newsletter2**  you must skip the first two steps.

1. If you have the old newsletter module installed uninstall it.

2. If you applied the previous step, run this SQL statement (using sql_console or phpMyAdmin), replace the **{prefix}**  placeholder.

```sql
insert into `{prefix}dbtrack` 
(component, name, date) 
values ('module/newsletter2', '001.sql', current_timestamp());
```

3. Install the **newsletter2**  module. Upload the package file **newsletter2-1.0.sin**  .

## Configuration

1. Insert the **newsletter2**  module into a page to offer newsletter registration for your users.

2. Give groups the required permissions to work with **newsletter2** 

3. Open the **newsletter2**  user interface.

4. Click **Edit Template**  .

5. Set a default title and a basic mail template. The mail template should include a mail footer with an **unsubscribe link**  and a **mail imprint.**  You may use **placeholders**  in your newsletter template. The available placeholders are described in a section below.

### Permissions

[...]

### Newsletter Placeholders

[...]

### Mail Queue

**newsletter2**  will use the **mail_queue**  module if it is installed