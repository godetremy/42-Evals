from github import Github
import os
from datetime import datetime
import re

PERSONAL_ACCESS_TOKEN = os.getenv('PERSONAL_ACCESS_TOKEN')
REPO_NAME = "rphlr/42-Evals"

def get_latest_stargazer_info(repo_name):
    g = Github(PERSONAL_ACCESS_TOKEN)
    repo = g.get_repo(repo_name)
    stargazers = list(repo.get_stargazers())
    if stargazers:
        last_stargazer = stargazers[-1]
        return last_stargazer.login, last_stargazer.avatar_url, last_stargazer.html_url
    return None, None, None


def update_readme(repo_name, last_stargazer, last_stargazer_avatar, last_stargazer_url):
    g = Github(PERSONAL_ACCESS_TOKEN)
    repo = g.get_repo(repo_name)
    contents = repo.get_contents("README.md")
    readme_text = contents.decoded_content.decode()

    now = datetime.now().strftime("%Y-%m-%d %H:%M:%S")

    # Utilisez les expressions régulières pour remplacer les valeurs dans les marqueurs
    readme_text = re.sub(r'<!--last_stargazer_start-->.*?<!--last_stargazer_end-->', 
                        f'<!--last_stargazer_start-->\n[![Last Stargazer]({last_stargazer_avatar}&s=250)]({last_stargazer_url})\n<!--last_stargazer_end-->', 
                        readme_text, flags=re.DOTALL)

    readme_text = re.sub(r'<!--name_start-->.*?<!--name_end-->', 
                        f'<!--name_start-->[{last_stargazer}]({last_stargazer_url})<!--name_end-->', 
                        readme_text, flags=re.DOTALL)

    readme_text = re.sub(r'<!--date_start-->.*?<!--date_end-->', 
                        f'<!--date_start-->{now}<!--date_end-->', 
                        readme_text, flags=re.DOTALL)


    repo.update_file(contents.path, "Automated README update with stargazer info and timestamp", readme_text, contents.sha)


if __name__ == "__main__":
    last_stargazer, last_stargazer_avatar, last_stargazer_url = get_latest_stargazer_info(REPO_NAME)
    if last_stargazer and last_stargazer_avatar and last_stargazer_url:
        update_readme(REPO_NAME, last_stargazer, last_stargazer_avatar, last_stargazer_url)