```
=========_==__==_======_======
 __ __ _(_)/ _|(_)  __| |___ 
 \ V  V / |  _|| |_/ _` / -_)
  \_/\_/|_|_|(_)_(_)__,_\___|
==============================
[ myWifide ] IDE @ Wi-Fi for MySQL
==============================


---------_--__--_------_------
          Overview
------------------------------

myWifide is simple IDE for MySQL. It is designed
to work on Wi-Fi network or single user hosting.

myWifide is ready to use on Testor Gifts such as:
+ Testor Cloud Personal Gift: https://progorker.github.io/sponsor/testor.cloud.personal.html
+ Testor Cloud Startup Gift: https://progorker.github.io/sponsor/testor.cloud.startup.html
+ Testor Progod Personal Gift: https://progorker.github.io/sponsor/testor.progod.personal.html


---------_--__--_------_------
 Commands Not In MySQL Client
------------------------------

+-----------+----------+-----------------------------------------------------------------------------------------------------+
| command   | shortcut | description                                                                                         |
+-----------+----------+-----------------------------------------------------------------------------------------------------+
| pattern   |          | Get code pattern from myTestor.                                                                     |
| workdir   |          | Set work dir. Argument is selected directory.                                                       |
| upload    |          | Upload zip file.                                                                                    |
| download  |          | Zip folder & download zip file. Argument is relative path.                                          |
| load      |          | Load script file into script editor. Argument is relative path.                                     |
| list      |          | List buffer directory. Argument is relative path.                                                   |
| remove    |          | Remove file. Argument is relative path.                                                             |
| save      |          | Save previous code to file. Does not execute script. Argument is relative path.                     |
| cat       |          | Display script file. Does not execute script. Argument is relative path.                            |
| open      | (\o)     | Open remote database. Arguments are host, port, username, password, database. Execute below script. |
+-----------+----------+-----------------------------------------------------------------------------------------------------+


---------_--__--_------_------
         Screenshots
------------------------------
```

![](https://github.com/progorker/pgk_mywifide/blob/main/myWifide/shots/shot_001.png?raw=true)

![](https://github.com/progorker/pgk_mywifide/blob/main/myWifide/shots/shot_002.png?raw=true)

![](https://github.com/progorker/pgk_mywifide/blob/main/myWifide/shots/shot_003.png?raw=true)

![](https://github.com/progorker/pgk_mywifide/blob/main/myWifide/shots/shot_004.png?raw=true)

![](https://github.com/progorker/pgk_mywifide/blob/main/myWifide/shots/shot_005.png?raw=true)

![](https://github.com/progorker/pgk_mywifide/blob/main/myWifide/shots/shot_006.png?raw=true)

![](https://github.com/progorker/pgk_mywifide/blob/main/myWifide/shots/shot_007.png?raw=true)

![](https://github.com/progorker/pgk_mywifide/blob/main/myWifide/shots/shot_008.png?raw=true)

![](https://github.com/progorker/pgk_mywifide/blob/main/myWifide/shots/shot_009.png?raw=true)

```
---------_--__--_------_------
          Changes
------------------------------

---| 2026.06.16 |---

+ Add 'download' command
+ Add 'load' command
+ Add 'locking' feature


---------_--__--_------_------
     Supported Commands
------------------------------

+-----------+----------+-----------------------------------------------------------------------------------------------------+
| command   | shortcut | description                                                                                         |
+-----------+----------+-----------------------------------------------------------------------------------------------------+
| ?         | (\?)     | Synonym for `help'.                                                                                 |
| charset   | (\C)     | Switch to another charset. Might be needed for processing binlog with multi-byte charsets.          |
| clear     | (\c)     | Clear the current input statement.                                                                  |
| connect   | (\r)     | Reconnect to the server. Optional arguments are db and host.                                        |
| delimiter | (\d)     | Set statement delimiter.                                                                            |
| ego       | (\G)     | Send command to MariaDB server, display result vertically.                                          |
| exit      | (\q)     | Exit mysql. Same as quit.                                                                           |
| go        | (\g)     |  Send command to MariaDB server.                                                                    |
| help      | (\h)     | Display this help.                                                                                  |
| nopager   | (\n)     | Disable pager, print to stdout.                                                                     |
| nowarning | (\w)     | Don't show warnings after every statement.                                                          |
| pager     | (\P)     | Set PAGER [to_pager]. Print the query results via PAGER.                                            |
| print     | (\p)     | Print current command.                                                                              |
| prompt    |  (\R)    | Change your mysql prompt.                                                                           |
| quit      | (\q)     | Quit mysql.                                                                                         |
| costs     | (\Q)     | Toggle showing query costs after each query                                                         |
| source    | (\.)     | Execute an SQL script file. Takes a file name as an argument.                                       |
| status    | (\s)     | Get status information from the server.                                                             |
| use       | (\u)     | Use another database. Takes database name as argument.                                              |
| warnings  | (\W)     | Show warnings after every statement.                                                                |
| pattern   |          | Get code pattern from myTestor.                                                                     |
| workdir   |          | Set work dir. Argument is selected directory.                                                       |
| upload    |          | Upload zip file.                                                                                    |
| download  |          | Zip folder & download zip file. Argument is relative path.                                          |
| load      |          | Load script file into script editor. Argument is relative path.                                     |
| list      |          | List buffer directory. Argument is relative path.                                                   |
| remove    |          | Remove file. Argument is relative path.                                                             |
| save      |          | Save previous code to file. Does not execute script. Argument is relative path.                     |
| cat       |          | Display script file. Does not execute script. Argument is relative path.                            |
| open      | (\o)     | Open remote database. Arguments are host, port, username, password, database. Execute below script. |
+-----------+----------+-----------------------------------------------------------------------------------------------------+

```
