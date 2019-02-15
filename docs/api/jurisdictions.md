---
title: Jurisdictions
---

The App does not have the concept of "background management". All data is judged by the client using Jurisdictions, and the corresponding permission function appears. Therefore, after the user logs in to the application, the App should obtain the user's Jurisdictions for function implementation.

[[toc]]

## Get all and user jurisdictions

```
GET {uri}/jurisdictions
```

Response:
```json
Status: 200 OK
{
    "all": [
        {
            "node": "forum:threads-manage",
            "name": "Forum > Threads Manage",
            "desc": "Manage Forum Threads"
        },
        {
            "node": "user:jurisdiction",
            "name": "User > Jurisdiction",
            "desc": "Change User Jurisdiction Nodes"
        }
    ],
    "user": [
        {
            "node": "user:jurisdiction",
            "name": "User > Jurisdiction",
            "desc": "Change User Jurisdiction Nodes"
        }
    ]
}
```

## Sync user all jurisdictions

::: warning
Need jurisdiction:`user:jurisdiction`
:::

```
PUT {uri}/users/{user}/jurisdictions
```

Input:
| Name | Type | Description |
|----|----|----|
| `nodes` | `array<object>` | Nodes that need to be synchronized. |

E.g:
```json
{
    "nodes": [
        { "node": "forum:threads-manage" },
        { "node": "user:jurisdiction" }
    ]
}
```

Response:
```
Status: 204 No Content
```

## Attach a single jurisdiction on an user

::: warning
Need jurisdiction:`user:jurisdiction`
:::

```
PUT {uri}/users/{user}/jurisdictions/{node}
```

Response:
```
Status: 204 No Content
```

## Detach a single jurisdiction on an user

::: warning
Need jurisdiction:`user:jurisdiction`
:::

```
DELETE {uri}/users/{user}/jurisdictions/{node}
```

Response:
```
Status: 204 No Content
```
