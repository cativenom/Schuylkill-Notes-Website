import praw
import requests
from os import getenv
from dotenv import load_dotenv
from mssql_python import connect

load_dotenv()

reddit = praw.Reddit(
    client_id=getenv("client_id"),
    client_secret=getenv("client_secret"),
    username=getenv("username"),
    user_agent=getenv("user_agent"),
    password=getenv("password"),
)
reddit.read_only = True
subreddit = reddit.subreddit("schuylkillnotes")
for submission in subreddit.hot(limit=12):
    print(submission.title)
    if "jpg" in submission.url.lower() or "png" in submission.url.lower():
        try:
            resp = requests.get(submission.url.lower(), stream=True)
            filename = submission.id + ".jpg"
            with open(filename, "wb") as f:
                for chunk in resp.iter_content(chunk_size=1024):
                    if chunk:
                        f.write(chunk)
            print("Image saved:", filename)
        except Exception as e:
            print("Image failed", e)
    


