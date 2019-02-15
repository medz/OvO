---
title: Upload File
---

```
POST {uri}/upload
```

Input:
| Name | Type | Description |
|----|----|----|
| `file` | `*File` | Files that need to be uploaded |

Response:
```
Status: 201 Created
{
    "filename": "image/1.png",
    "url": "https://fans.io/storage/image/1.png"
}
```

