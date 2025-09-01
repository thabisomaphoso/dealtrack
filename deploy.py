import os
import subprocess

# Config
GITHUB_URL = "https://github.com/ThabisoMaphoso/dealtrack.git"  # update if needed
BRANCH = "main"

def run_cmd(cmd, allow_fail=False):
    print(f"▶ {cmd}")
    try:
        subprocess.run(cmd, shell=True, check=True)
    except subprocess.CalledProcessError as e:
        if allow_fail:
            print(f"⚠️ Command failed but continuing: {e}")
        else:
            raise

def main():
    # Ensure git repo is initialized
    if not os.path.exists(".git"):
        run_cmd("git init")

    # Add all files & commit
    run_cmd("git add .")
    run_cmd('git commit -m "Deploy update" || echo "No changes to commit"', allow_fail=True)

    # Link remote if missing
    remotes = subprocess.check_output("git remote -v", shell=True).decode()
    if "origin" not in remotes:
        run_cmd(f"git remote add origin {GITHUB_URL}")
        run_cmd(f"git branch -M {BRANCH}")

    # Always pull before pushing to avoid rejections
    run_cmd(f"git pull origin {BRANCH} --rebase", allow_fail=True)

    # Push to GitHub
    run_cmd(f"git push -u origin {BRANCH}")

    # Deploy to Vercel (requires `vercel` CLI installed + logged in)
    run_cmd("vercel --prod")

if __name__ == "__main__":
    main()
