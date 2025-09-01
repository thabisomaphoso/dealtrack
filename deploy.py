import os
import subprocess

# Config
GITHUB_URL = "https://github.com/thabisomaphoso/dealtracksa.git"
BRANCH = "main"

def run_cmd(cmd):
    print(f"▶ {cmd}")
    subprocess.run(cmd, shell=True, check=True)

def main():
    # Ensure git repo is initialized
    if not os.path.exists(".git"):
        run_cmd("git init")

    # Add all files & commit
    run_cmd("git add .")
    run_cmd('git commit -m "Deploy update" || echo "No changes to commit"')

    # Link remote if missing
    remotes = subprocess.check_output("git remote -v", shell=True).decode()
    if "origin" not in remotes:
        run_cmd(f"git remote add origin {GITHUB_URL}")
        run_cmd(f"git branch -M {BRANCH}")

    # Push to GitHub
    run_cmd(f"git push -u origin {BRANCH}")

    # Deploy to Vercel (requires `vercel` CLI installed + logged in)
    run_cmd("vercel --prod")

if __name__ == "__main__":
    main()
