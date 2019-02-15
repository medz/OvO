---
title: Nodes
---

## List all nodes

```
GET {uri}/forum/nodes
```

Response:
```json
Status: 200 OK
[
    {
        "id": 1,
        "name": "Node 1",
        "description": null,
        "icon": null,
        "color": null,
        "counts": {
            "threads": 0,
            "followers": 0
        }
    }
]
```

## Get a single node

```
GET {uri}/forum/nodes/{id}
```

Response:
```json
Status: 200 OK
{
    "id": 1,
    "name": "Node 1",
    "description": null,
    "icon": null,
    "color": null,
    "counts": {
        "threads": 0,
        "followers": 0
    }
}
```

## Create on an node

```
POST {uri}/forum/nodes
```

Input:
| Name | Type | Description |
|----|----|----|
| `name` | `string` | **Required**, The name of the node. |
| `description` | `string` | The description of the node. |
| `color` | `string` | Set the node background color. |
| `icon` | `string` | The icon is image filename. |

Response:
```json
Status: 201 Created
{
    "id": 1,
    "name": "Node 1",
    "description": null,
    "icon": null,
    "color": null,
    "counts": {
        "threads": 0,
        "followers": 0
    }
}
```

## Edit a node

::: warning
Need jurisdiction:`forum:nodes-manage`.
:::

```
PUT|PATCH {uri}/forum/nodes/{id}
```

Input:
| Name | Type | Description |
|----|----|----|
| `name` | `string` | The name of the node. |
| `description` | `string` | The description of the node. |
| `color` | `string` | Set the node background color. |
| `icon` | `string` | The icon is image filename. |

Response:
```
Status: 204 No Content
```

## Delete a node

::: warning
Need jurisdiction:`forum:nodes-manage`.
:::

```
DELETE {uri}/forum/nodes/{id}
```

Input:
| Name | Type | Description |
|----|----|----|
| `node` | `integer` | The thread under the deleted node needs to be moved to the specified node. |

Response:
```
Status: 204 No Content
```
