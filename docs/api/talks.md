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
| `query` | `string` | Full-text matching search, automatically sorted according to the matching degree, the priority is lower than the `id` parameter, and the other parameters of the `query` parameter are only available for `page`. |
| `page` | `integer` | Page number. |
| `direction` | `string` | Support `asc` and `desc` according to the sorting direction of the comment `id`. |
| `publisher` | `integer` | Get the specified user data. |

Response:
```json
Status: 200 OK
{
    "data": [
        {
            "id": 1,
            "content": "The is a talk.",
            "publisher_id": 1,
            "resource": {
                "type": "video",
                "video": "https://fans.io/storage/videos/1.ogv"
            },
            "repostable": {
                "type": "forum:threads",
                "id": 1,
                "forum:thread": {}
            },
            "publisher": {},
            "created_at": "2019-01-30T14:17:22Z",
            "counts": {
                "comments": 0,
                "likes": 0,
                "views": 1
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
        "from": null,
        "last_page": 1,
        "path": "http://fans.local.medz.cn/talks",
        "per_page": 10,
        "to": null,
        "total": 0
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
    "id": 1,
    "content": "The is a talk.",
    "publisher_id": 1,
    "resource": {
        "type": "video",
        "video": "https://fans.io/storage/videos/1.ogv"
    },
    "repostable": {
        "type": "forum:threads",
        "id": 1,
        "forum:thread": {}
    },
    "publisher": {},
    "created_at": "2019-01-30T14:17:22Z",
    "counts": {
        "comments": 0,
        "likes": 0,
        "views": 1
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
| `repostable` | `object` | Repost respurce data, <br/> E.g: `{"type": "talks", "id": 1}`. |
| `resource_type` | `string` | Append respurce type, Only `images`/`video` and `link`. |
| `resource` | `array<string>|string` | The resource data of the talk. |

E.g:
```json
{
    "content": "The is a talk.",
    "repostable": {
        "type": "talks",
        "id": 1
    },
    "resource_type": "link",
    "resource": "https://medz.cn"
}
```

Response:
```json
Status: 201 Created
{
    "id": 2,
    "content": "The is a talk.",
    "publisher_id": 1,
    "resource": {
        "type": "link",
        "video": "https://medz.cn"
    },
    "repostable": {
        "type": "talks",
        "id": 1,
        "talk": {
            "id": 1,
            "content": "The is a talk.",
            "publisher_id": 1,
            "resource": {
                "type": "video",
                "video": "https://fans.io/storage/videos/1.ogv"
            },
            "repostable": {
                "type": "forum:threads",
                "id": 1,
            },
            "publisher": {},
            "created_at": "2019-01-30T14:17:22Z",
            "counts": {
                "comments": 0,
                "likes": 0,
                "views": 1
            }
        }
    },
    "publisher": {},
    "created_at": "2019-01-30T14:17:22Z",
    "counts": {
        "comments": 0,
        "likes": 0,
        "views": 1
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
