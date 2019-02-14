---
title: Authorizations
---

You can use this API to register and log in to the account. We merged the registration and login steps, so you only need to send a request to get the JWT token.

[[toc]]

## Issue token

```
POST {uri}/auth/jwt
```

Input:

| Name | Type | Description |
|----|----|----|
| `international_telephone_code` | `string` | See👉 [ITC](itc.md) codes. |
| `phone` | `string` | Phone number in the [ITC](itc.md) region. |
| `verify_type` | `string` | Fixed to `phone`. | 
| `verification_code` | `integer` | Verification code sent to the phone. |

```json
{
    "international_telephone_code": "+86",
    "phone": "1878****582",
    "verify_type": "phone",
    "verification_code": 64822
}
```

Response:
```json
Status: 200 OK
{
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczovL3dlaW1lbmc0LnpoaWJvY2xvdWQuY24vYXBpL3YyL2F1dGgvbG9naW4iLCJpYXQiOjE1NTAxMTAwODUsImV4cCI6MTU1MTQwNjA4NSwibmJmIjoxNTUwMTEwMDg1LCJqdGkiOiJJaFBnZm9uVWJtTUtRM1doIiwic3ViIjoxNiwicHJ2IjoiNDhlNDUzODMxY2ViYTVlNTdhNDc1ZTY4NjQ5Y2ZkZWU2ZTk3ZDhkMiJ9.ZWd9LgZ_brOmQ92lZTuBu7dDA3CEtNIVPk_Jjx325mk",
    "token_type": "Bearer",
    "expires_in": 21600
}
```

## Get the authenticated user

```
GET {uri}/auth/me
```

Response with public and private profile information

```json
Status: 200 OK
{
    "id": 1,
    "avatar": "https://fans.io/storage/2019/22/av.png",
    "phone": {
        "number": "1878****582",
        "international_telephone_code": "+86"
    },
    "created_at": "2019-01-30T14:17:22Z",
    "extras": [
        /// ...
    ]
}
```

## Send verification code

```
POST {uri}/auth/verify-code
```

Input:

 Name | Type | Description |
|----|----|----|
| `international_telephone_code` | `string` | See👉 [ITC](itc.md) codes. |
| `phone` | `string` | Phone number in the [ITC](itc.md) region. |

Response:

```
Status: 204 No Content
```
