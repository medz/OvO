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
curl {uri}/users/1
```

Response:
```json
Status: 200 OK
{
    "id": 1,
    "avatar": null,
    "created_at": "2019-01-30T14:17:22Z",
    "extras": []
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
| `sort` | `string` | Enumeration only for `id`/`phone`/`name`/`international_telephone_code`. |
| `direction` | `string` | Use `sort` to specify the field for direction sorting, support: `asc` and `desc`. |
| `page` | `integer` | Page number. |

E.g:
```sh
curl {uri}/users?id[]=1&id[]=2
```

Response:
```json
Status: 200 OK
{
    "data": [
        {
            "id": 1,
            "avatar": null,
            "created_at": "2019-01-30T14:17:22Z"
        },
        {
            "id": 2,
            "avatar": null,
            "created_at": "2019-01-30T14:17:23Z"
        }
    ],
    "links": {
        "first": "http://fans.local.medz.cn/users?page=1",
        "last": "http://fans.local.medz.cn/users?page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "path": "http://fans.local.medz.cn/users",
        "per_page": 10,
        "to": 1,
        "total": 1
    }
}
```
