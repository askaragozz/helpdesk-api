http PATCH :8000/api/v1/tickets/1/status "Authorization:Bearer 5|e8JJYbnZG500GX1uDmHj3cKb0fult9hk42nCBasHce378ac5" status=in_progress
http PATCH :8000/api/v1/tickets/1/status "Authorization:Bearer 5|e8JJYbnZG500GX1uDmHj3cKb0fult9hk42nCBasHce378ac5" status=resolved
http PATCH :8000/api/v1/tickets/1/status "Authorization:Bearer 5|e8JJYbnZG500GX1uDmHj3cKb0fult9hk42nCBasHce378ac5" status=closed


http PATCH :8000/api/v1/tickets/1/status "Authorization:Bearer 5|e8JJYbnZG500GX1uDmHj3cKb0fult9hk42nCBasHce378ac5" status=in_progress


http :8000/api/v1/tickets/1/status-history "Authorization:Bearer 5|e8JJYbnZG500GX1uDmHj3cKb0fult9hk42nCBasHce378ac5"


http :8000/api/v1/tickets "Authorization:Bearer 4|9yu59AiEKrN4gvFeUDrt7JDMMf4khBZi6CdFWe9vbd2036f4" scope==assigned
http :8000/api/v1/tickets "Authorization:Bearer 5|e8JJYbnZG500GX1uDmHj3cKb0fult9hk42nCBasHce378ac5" scope==unassigned
http :8000/api/v1/tickets "Authorization:Bearer 5|e8JJYbnZG500GX1uDmHj3cKb0fult9hk42nCBasHce378ac5" scope==all
http :8000/api/v1/tickets "Authorization:Bearer 5|e8JJYbnZG500GX1uDmHj3cKb0fult9hk42nCBasHce378ac5" scope==assigned status==open


http :8000/api/v1/tickets/1/comments "Authorization:Bearer 5|e8JJYbnZG500GX1uDmHj3cKb0fult9hk42nCBasHce378ac5"
http POST :8000/api/v1/tickets/1/comments "Authorization:Bearer 5|e8JJYbnZG500GX1uDmHj3cKb0fult9hk42nCBasHce378ac5" body="Hello" visibility=public
http POST :8000/api/v1/tickets/1/comments/read "Authorization:Bearer 5|e8JJYbnZG500GX1uDmHj3cKb0fult9hk42nCBasHce378ac5"
