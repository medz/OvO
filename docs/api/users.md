---
title: Users
---

::: danger
The current users API stereotype is still open to question, only the finalized API documentation is available.
:::

[[toc]]

## Get a single user

Provides publicly available information about someone with a app account.

```
GET {uri}/users/{id}
```

E.g:
```sh
curl {uri}/users/31dcf180-e3fe-4d7a-baa0-82a51fd2c3c3
```

Response:
```json
Status: 200 OK
{
    "id":"31dcf180-e3fe-4d7a-baa0-82a51fd2c3c3",
    "created_at":"2019-03-08T07:40:39Z",
    "extras":[

    ]
}
```

## Get all users

Lists all users, in the order that they signed up on app. This list only includes personal user accounts.

```
GET {uri}/users
```

Query parameters:
| Name | Type | Description |
|----|----|----|
| `id` | `array` | Only respond to the given ID list data. If this data is given, other parameters except the `page` parameter will be ignored. |
| `page` | `integer` | Page number. |

Admin more query parameters:
| Name | Type | Description |
|----|----|----|
| `name` | `string` | Likely user name. |
| `phone` | `string` | User phone. |
| `itc` | `string` | User ITC |

E.g:
```sh
curl {uri}/users?id[]=31dcf180-e3fe-4d7a-baa0-82a51fd2c3c3
```

Response:
```json
Status: 200 OK
{
    "data":[
        {
            "id":"31dcf180-e3fe-4d7a-baa0-82a51fd2c3c3",
            "created_at":"2019-03-08T07:40:39Z"
        }
    ],
    "links":{
        "first":"http://fans.local.medz.cn/users?page=1",
        "last":"http://fans.local.medz.cn/users?page=1",
        "prev":null,
        "next":null
    },
    "meta":{
        "current_page":1,
        "from":1,
        "last_page":1,
        "path":"http://fans.local.medz.cn/users",
        "per_page":10,
        "to":1,
        "total":1
    }
}
```
