---
title: International Telephone Codes
---

[[toc]]

## Get all ITC

```
GET {uri}/international-telephone-codes
```

Response:

```json
Status: 200 OK
[
    {
        "id": 1,
        "code": "+86",
        "name": "中国",
        "icon": "🇨🇳",
        "enabled": true
    }
]
```

## Create an ITC

::: warning
Any user with manage(`ttc:manage`) access to a ITC can create an ITC.
:::

```
POST {uri}/international-telephone-codes
```

Input:
| Name | Type | Description |
|----|----|----|
| `code` | `string` | International telephone code. |
| `name` | `string` | The name of the ITC. |
| `icon` | `string` | The icon is a Emoji of the ITC. |
| `enabled` | `boolean` | Is this ITC enabled. |

Example:
```json
{
    "code": "+86",
    "name": "中国",
    "icon": "🇨🇳",
    "enabled": true
}
```

Response:
```json
Status: 201 Created
{
    "id": 1,
    "code": "+86",
    "name": "中国",
    "icon": "🇨🇳",
    "enabled": true
}
```

## Edit an ITC

::: warning
Any user with manage(`ttc:manage`) access to a ITC can Edit an ITC.
:::

```
PATCH {uri}/international-telephone-codes/{id}
```

Input:
| Name | Type | Description |
|----|----|----|
| `code` | `string` | International telephone code. |
| `name` | `string` | The name of the ITC. |
| `icon` | `string` | The icon is a Emoji of the ITC. |
| `enabled` | `boolean` | Is this ITC enabled. |

Example:
```json
{
    "enabled": false
}
```

Response:
```
Status: 204 No Content
```

## Delete a ITC

::: warning
Any user with manage(`ttc:manage`) access to a ITC can Delete a ITC.
:::

```
DELETE {uri}/international-telephone-codes/{id}
```

Response:
```
Status: 204 No Content
```
