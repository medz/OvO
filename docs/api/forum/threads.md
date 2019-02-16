---
title: Threads
---

## Get all threads

```
GET {uri}/forum/threads
```

Query Parameters:
| Name | Type | Description |
|----|----|----|
| `id` | `array` | Only respond to the given ID list data. If this data is given, other parameters except the `page` parameter will be ignored. |
| `query` | `string` | Full-text matching search, automatically sorted according to the matching degree, the priority is lower than the `id` parameter, and the other parameters of the `query` parameter are only available for `page`. |
| `page` | `integer` | Page number. |
| `direction` | `string` | Support `asc` and `desc` according to the sorting direction of the comment `id`. |
| `publisher` | `integer` | Get the specified user data. |
| `node` | `integer` | Filter threads under the specified node. |

Response:
```json
Status: 200 OK
{
    "data": [
        {
            "id": 1,
            "publisher_id": 1,
            "node_id": 1,
            "state": {
                "published": true,
                "excellent": false,
                "pinned": false,
            },
            "counts": {
                "views": 1,
                "likes": 3,
                "comments": 0
            },
            "created_at": "2019-01-30T14:17:23Z",
            "title": "The is thread title.",
            "publisher": {},
            "node": {}
        }
    ],
    "links": {
        "first": "http://fans.local.medz.cn/forum/threads?page=1",
        "last": "http://fans.local.medz.cn/forum/threads?page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": null,
        "last_page": 1,
        "path": "http://fans.local.medz.cn/forum/threads",
        "per_page": 10,
        "to": null,
        "total": 0
    }
}
```

## Get a single thread

```
GET {uri}/forum/threads/{id}
```

Response:
```json
Status: 200 OK
{
    "id": 1,
    "publisher_id": 1,
    "node_id": 1,
    "state": {
        "published": true,
        "excellent": false,
        "pinned": false,
    },
    "counts": {
        "views": 1,
        "likes": 3,
        "comments": 0
    },
    "created_at": "2019-01-30T14:17:23Z",
    "title": "The is thread title.",
    "publisher": {},
    "node": {}
}
```

## Create on an thread

```
POST {uri}/forum/nodes/{node}/threads
```

Input:
| Name | Type | Description |
|----|----|----|
| `title` | `string` | **Required**, The title of the thread. |
| `content` | `string` | The content of the thread. |

Response:
```json
Status: 201 Created
{
    "id": 1,
    "publisher_id": 1,
    "node_id": 1,
    "state": {
        "published": true,
        "excellent": false,
        "pinned": false,
    },
    "counts": {
        "views": 1,
        "likes": 3,
        "comments": 0
    },
    "created_at": "2019-01-30T14:17:23Z",
    "title": "The is thread title.",
    "publisher": {},
    "node": {}
}
```

## Edit a thread

::: warning
Need jurisdiction:`forum:threads-manage` or **the authorizationed user is publisher**.
:::

```
PUT|PATCH {uri}/forum/threads/{id}
```

Input:
| Name | Type | Description |
|----|----|----|
| `title` | `string` | The title of the thread. |
| `content` | `string` | The content of the thread. |

Response:
```
Status: 204 No Content
```

## Delete a thread

::: warning
Need jurisdiction:`forum:threads-manage` or **the authorizationed user is publisher**.
:::

```
DELETE {uri}/forum/threads/{id}
```

Response:
```
Status: 204 No Content
```

## Move one thread to another node

::: warning
Need jurisdiction:`forum:threads-manage` or **the authorizationed user is publisher**.
:::

```
PUT {uri}/forum/nodes/{node}/threads/{thread}
```

> `{node}` is the node ID that the topic needs to be transferred in, `{thread}` is the thread ID being operated on.

Response:
```
Status: 204 No Content
```
