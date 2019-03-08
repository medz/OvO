---
title: Talks
---

## List all talks

```
GET {uri}/talks
```

Query Parameters:
| Name | Type | Description |
|----|----|----|
| `id` | `array` | Only respond to the given ID list data. If this data is given, other parameters except the `page` parameter will be ignored. |
| `page` | `integer` | Page number. |
| `publisher` | `array` | Get the specified user data. |

Response:
```json
Status: 200 OK
{
    "data": [
        {
            "id": "c2d71eba-ed3b-4f44-84fc-5ae9a03b26b3",
            "publisher_id": "8bb0cdd1-99c1-4e56-8254-2e97283114c8",
            "content": "第一个 talk",
            "created_at": "2019-03-08T10:13:03Z",
            "counts": {
                "views": 1,
                "likes": 0,
                "comments": 0,
                "shares": 0
            }
        }
    ],
    "links": {
        "first": "http://fans.local.medz.cn/talks?page=1",
        "last": "http://fans.local.medz.cn/talks?page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "path": "http://fans.local.medz.cn/talks",
        "per_page": 10,
        "to": 1,
        "total": 1
    }
}
```

## Get a single talk

```
GET {uri}/talks/{id}
```

Response:
```json
Status: 200 OK
{
    "id": "c2d71eba-ed3b-4f44-84fc-5ae9a03b26b3",
    "publisher_id": "8bb0cdd1-99c1-4e56-8254-2e97283114c8",
    "content": "第一个 talk",
    "created_at": "2019-03-08T10:13:03Z",
    "counts": {
        "views": 1,
        "likes": 0,
        "comments": 0,
        "shares": 0
    }
}
```

## Create on an talk

```
POST {uri}/talks
```

Input:
| Name | Type | Description |
|----|----|----|
| `content` | `string` | **Required**, The content of the talk. |
| `shareable` | `object` | Repost respurce data, <br/> E.g: `{"type": "talks", "id": "c2d71eba-ed3b-4f44-84fc-5ae9a03b26b3"}`. |
| `media` | `array<string>` | The resource data of the talk. |

E.g:
```json
{
    "content": "The is a talk.",
    "shareable": {
        "type": "talks",
        "id": "c2d71eba-ed3b-4f44-84fc-5ae9a03b26b3"
    },
    "media": [
        "demo/ahaha/ddd.mp4"
    ]
}
```

Response:
```json
Status: 201 Created
{
    "id": "c2d71eba-ed3b-4f44-84fc-5ae9a03b26b3",
    "publisher_id": "8bb0cdd1-99c1-4e56-8254-2e97283114c8",
    "content": "第一个 talk",
    "created_at": "2019-03-08T10:13:03Z",
    "counts": {
        "views": 1,
        "likes": 0,
        "comments": 0,
        "shares": 0
    }
}
```

## Delete a talk

::: warning
Need jurisdiction:`talk:destroy` or **the authorizationed user is publisher**.
:::

```
DELETE {uri}/talks/{id}
```

Response:
```
Status: 204 No Content
```
