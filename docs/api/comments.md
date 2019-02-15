---
title: Comments
---

This Comments API supports listing [Talks](talks.md) and [Threads](forum/threads.md), as well as creating comments for [Talks](talks.md) and [Threads](forum/threads.md).

[[toc]]

## List comments on an [Talk](talks.md)

```
GET {uri}/comments?commentable_type=talks
```

Query Parameters:
| Name | Type | Description |
|----|-----|----|
| `commentable_id` | `integer` | **Required**, A Talk ID. |
| `direction` | `string` | Support `asc` and `desc` according to the sorting direction of the comment `id`. |
| `page` | `integer` | Page number. |

Response:
```json
Status: 200 OK
{
    "data": [
        {
            "id": 1,
            "publisher_id": 1,
            "content": "The is a comment.",
            "created_at": "2019-01-30T14:17:23Z",
            "commentable": {
                "typr": "talks",
                "id": 1,
            },
            "resource": {
                "type": "video",
                "video" "http://fans.local.medz.cn/video.mp4"
            }
        }
    ],
    "links": {
        "first": "http://fans.local.medz.cn/comments?page=1",
        "last": "http://fans.local.medz.cn/comments?page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": null,
        "last_page": 1,
        "path": "http://fans.local.medz.cn/comments",
        "per_page": 10,
        "to": null,
        "total": 0
    }
}
```

## List comments in a [Thread](forum/threads.md)

```
GET {uri}/comments?commentable_type=forum:threads
```

Query Parameters:
| Name | Type | Description |
|----|-----|----|
| `commentable_id` | `integer` | **Required**, A forum Thread ID. |
| `direction` | `string` | Support `asc` and `desc` according to the sorting direction of the comment `id`. |
| `page` | `integer` | Page number. |

Response:
```json
Status: 200 OK
{
    "data": [
        {
            "id": 1,
            "publisher_id": 1,
            "content": "The is a comment.",
            "created_at": "2019-01-30T14:17:23Z",
            "commentable": {
                "typr": "forum:threads",
                "id": 1,
            },
            "resource": {
                "type": "video",
                "video" "http://fans.local.medz.cn/video.mp4"
            }
        }
    ],
    "links": {
        "first": "http://fans.local.medz.cn/comments?page=1",
        "last": "http://fans.local.medz.cn/comments?page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": null,
        "last_page": 1,
        "path": "http://fans.local.medz.cn/comments",
        "per_page": 10,
        "to": null,
        "total": 0
    }
}
```

## Full text search

```
GET {uri}/comments?query={keyworkd}
```

Query Parameters:
| Name | Type | Description |
|----|-----|----|
| `page` | `integer` | Page number. |

Response:
```json
Status: 200 OK
{
    "data": [
        {
            "id": 1,
            "publisher_id": 1,
            "content": "The is a comment.",
            "created_at": "2019-01-30T14:17:23Z",
            "commentable": {
                "typr": "forum:threads",
                "id": 1,
            },
            "resource": {
                "type": "video",
                "video" "http://fans.local.medz.cn/video.mp4"
            }
        }
    ],
    "links": {
        "first": "http://fans.local.medz.cn/comments?page=1",
        "last": "http://fans.local.medz.cn/comments?page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": null,
        "last_page": 1,
        "path": "http://fans.local.medz.cn/comments",
        "per_page": 10,
        "to": null,
        "total": 0
    }
}
```

## Get all comments

::: tip
Talks and Threads and search comment lists are filtered using this interface query parameters.
:::

```
GET {uri}/comments
```

Query Parameters:
| Name | Type | Description |
|----|-----|----|
| `commentable_type` | `integer` | **Required with `commentable_id`**, Only `talks` and `forum:threads`. |
| `commentable_id` | `integer` | **Required with `commentable_type`**, A `commentable_type` resource ID. |
| `direction` | `string` | Support `asc` and `desc` according to the sorting direction of the comment `id`. |
| `publisher` | `integer` | Get only comments posted by this user. |
| `id` | `array` | Only respond to the given ID list data. If this data is given, other parameters except the `page` parameter will be ignored. |
| `query` | `string` | Full-text matching search, automatically sorted according to the matching degree, the priority is lower than the `id` parameter, and the other parameters of the `query` parameter are only available for `page`. |
| `page` | `integer` | Page number. |

Response:
```json
Status: 200 OK
{
    "data": [
        {
            "id": 1,
            "publisher_id": 1,
            "content": "The is a comment.",
            "created_at": "2019-01-30T14:17:23Z",
            "commentable": {
                "typr": "forum:threads",
                "id": 1,
            },
            "resource": {
                "type": "video",
                "video" "http://fans.local.medz.cn/video.mp4"
            }
        }
    ],
    "links": {
        "first": "http://fans.local.medz.cn/comments?page=1",
        "last": "http://fans.local.medz.cn/comments?page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": null,
        "last_page": 1,
        "path": "http://fans.local.medz.cn/comments",
        "per_page": 10,
        "to": null,
        "total": 0
    }
}
```

## Create a comment

```
POST {uri}/comments
```

Input:
| Name | Type | Description |
|----|----|----|
| `commentable_type` | `integer` | **Required**, Only `talks` and `forum:threads`. |
| `commentable_id` | `integer` | **Required**, A `commentable_type` resource ID. |
| `content` | `string` | **Required without `resource_type`**, The contents of the comment. |
| `resource_type` | `string` | **Required without `content`**, The comment `resource` type. Only `image`/`video` and `long-text`. |
| `resource` | `array|string` | **Required with `resource_type`**, The resource oof the comment. |

E.g:
```json
{
    "commentable_type": "forum:threads",
    "commentable_id": 1,
    "content": "The is comment content",
    "resource_type": "image",
    "resource": [
        "image/1.png",
        "image/2.png"
    ]
}
```

Response:
```json
Status: 201 Created
{
    "id": 1,
    "publisher_id": 1,
    "content": "The is comment content",
    "created_at": "2019-01-30T14:17:23Z",
    "commentable": {
        "typr": "forum:threads",
        "id": 1,
    },
    "resource": {
        "type": "image",
        "image": [
            "https://fans.io/storage/image/1.png",
            "https://fans.io/storage/image/2.png"
        ]
    }
}
```

## Delete a comment

```
DELETE {uri}/comments/{id}
```

Response:
```
Status: 204 No Content
```
