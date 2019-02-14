---
title: Overview
---

This describes the resources that make up the official REST API. If you have any problems or requests, please contact <a href="https://github.com/medz/fans/issues">Fans Support</a>.

[[toc]]

## Root endpoint

In the next document, `{{ $site.themeConfig.env.APP_URL }}` will be replaced with `{uri}`.

## Schema

All API access is over HTTP(S), and accessed from `{{ $site.themeConfig.env.APP_URL }}`. All data is sent and received as **JSON**.

```yaml
curl -i {uri}/auth/me
Transfer-Encoding: chunked
Cache-Control: no-cache, private
Content-Type: application/json
Date: Thu, 14 Feb 2019 14:11:32 GMT
Keep-Alive: timeout=38
Server: nginx/1.15.8
X-Powered-By: PHP/7.1.25
X-Ratelimit-Limit: 60
X-Ratelimit-Remaining: 58
```

Blank fields are included as null instead of being omitted.

All timestamps return in ISO 8601 format:

```
YYYY-MM-DDTHH:MM:SSZ
```

## Authentication

Sent in a header ways to authenticate through API. Requests that require authentication will return 404 Not Found, instead of 403 Forbidden, in some places. This is to prevent the accidental leakage of private data to unauthorized users.

```
curl -H "Authorization: Bearer ACCESS-TOKEN" {uri}
```

### Failed login limit

Authenticating with invalid credentials will return 401 Unauthorized:

```json
curl -H "Authorization: Bearer ACCESS-TOKEN" {uri}/auth/me
HTTP/1.1 401 Unauthorized
{
    "message": "Unauthenticated."
}
```

### Parameters

Many API methods take optional parameters. For GET requests, any parameters not specified as a segment in the path can be passed as an HTTP query string parameter:

```
curl -i "{uri}/forum/threads?node=1"
```

For POST, PATCH, PUT, and DELETE requests, **parameters not included in the URL should be encoded as JSON** with a Content-Type of `application/json`:

```sh
curl -i -d '{"phone":"1878*****73", "international_telephone_code": "+86"}' {uri}/auth/verify-code
```

## HTTP verbs

Where possible, API strives to use appropriate HTTP verbs for each action.

| Verb | Description |
|----|----|
| `HEAD` | Can be issued against any resource to get just the HTTP header info. |
| `GET` | Used for retrieving resources. |
| `POST` | Used for creating resources. |
| `PATCH` | Used for updating resources with partial JSON data. For instance, an Thread resource has `title` and `body` attributes. A PATCH request may accept one or more of the attributes to update the resource. PATCH is a relatively new and uncommon HTTP verb, so resource endpoints also accept `POST` requests. |
| `PUT` | Used for replacing resources or collections. For `PUT` requests with no `body` attribute, be sure to set the `Content-Length` header to zero. |
| `DELETE` | Used for deleting resources. |

### Simulation

Sometimes the client environment supports the use of HTTP verbs, so all APIs support emulation using `POST` verb passing. Use the `_method` parameter to simulate the HTTP verb that needs to be simulated. E.g:

```
curl -i -d '{"_method": "DELETE"}' {uri}/comments/1
```

## Rate limiting

The returned HTTP headers of any API request show your current rate limit status:

```yaml
curl -i {uri}/users
HTTP/1.1 200 OK
Date: Thu, 14 Feb 2019 14:11:32 GMT
Status: 200 OK
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 56
```
