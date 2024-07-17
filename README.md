CRUD Operations for Posts in functions.php
 
Create Post
Endpoint
URL: /wp-json/custom/v1/create-post
Method: POST
Parameters
title (required): Title of the post.
content (required): Content of the post.
category (optional): Category of the post. Defaults to 'shopping' if not provided.
Authorization
Requires JWT token in Authorization header.
Success Response: HTTP 200 OK
{
  "message": "Post created successfully"
}
Error Response: HTTP 500 Internal Server Error
{
  "error": "error_creating_post",
  "message": "Failed to create post: Error message"
}


Read Posts
Endpoint
URL: /wp-json/custom/v1/fetch-posts
Method: GET
Description: Fetches all published posts.
Authorization
Requires JWT token in Authorization header.
Success Response: HTTP 200 OK
{
  "success": true,
  "data": [
{
"ID": 1,
"post_title": "Sample Post Title",
"post_content": "Sample post content."
},
{
"ID": 2,
"post_title": "Another Post",
"post_content": "Content of another post."
}
// More posts...
]
}



Update Post
Endpoint
URL: /wp-json/custom/v1/update-post
Method: POST
Description: Updates an existing post.
Parameters
post_id (required): ID of the post to update.
post_title (required): New title of the post.
post_content (required): New content of the post.
category (optional): New category of the post.
Authorization
Requires JWT token in Authorization header.
Success Response: HTTP 200 OK
{
  "message": "Post updated successfully",
  "post_id": 1
}


Delete Post
Endpoint
URL: /wp-json/custom/v1/delete-post
Method: POST
Description: Deletes a post.
Parameters
post_id (required): ID of the post to delete.
Authorization
Requires JWT token in Authorization header.
Success Response: HTTP 200 OK
{
  "message": "Post deleted successfully"
}
Error Response: HTTP 404 Not Found
{
  "error": "not_found",
  "message": "No post found with this ID"
}



Api Integration in front-end
