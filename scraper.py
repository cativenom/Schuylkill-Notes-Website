import praw
import requests
from os import getenv
import os 
from dotenv import load_dotenv
from mssql_python import connect

load_dotenv()
image_folder = "noteimages"
reddit = praw.Reddit(
    client_id=getenv("client_id"),
    client_secret=getenv("client_secret"),
    username=getenv("username"),
    user_agent=getenv("user_agent"),
    password=getenv("password"),
)
reddit.read_only = True
subreddit = reddit.subreddit("schuylkillnotes")
for submission in subreddit.hot(limit=1):
    print(vars(submission))
    print(submission.title)
    print("Image URL:", submission.url)
    if hasattr(submission, "post_hint") and submission.post_hint == "image":
        image_url = submission.url
    elif hasattr(submission, "preview"):
        image_url = submission.preview['images'][0]['source']['url']
    elif hasattr(submission, "is_gallery") and submission.is_gallery:
        for media_id in submission.media_metadata:
            image_url = submission.media_metadata[media_id]['s']['u']
    else:
        image_url = None

    if image_url and ("jpg" in image_url.lower() or "png" in image_url.lower()):
        try:
            resp = requests.get(image_url.lower(), stream=True)
            filename = os.path.join(image_folder, submission.id + ".jpg")
            with open(filename, "wb") as f:
                for chunk in resp.iter_content(chunk_size=1024):
                    if chunk:
                        f.write(chunk)
            print("Image saved:", filename)
        except Exception as e:
            print("Image failed", e)


