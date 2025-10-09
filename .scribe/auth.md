
# Authenticating requests

To authenticate requests, include an **`Authorization`** header with the value **`"Bearer {YOUR_ACCESS_TOKEN}"`**.

All authenticated endpoints are marked with a `requires authentication` badge in the documentation below.

## How to get your access token:

1. **Login:** Send a POST request to `/api/v1/login` with your credentials
2. **Get token:** You'll receive an access token in the response
3. **Use token:** Include the token in all subsequent requests as shown below:

```bash
Authorization: Bearer YOUR_ACTUAL_TOKEN_HERE
```

## Testing in this documentation:

Click the **"Authorize"** button at the top of this page and enter your Bearer token. This will automatically add the Authorization header to all "Try it out" requests.
