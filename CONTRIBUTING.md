# Contributing Guidelines :sparkles:

Contributions are welcome!! 🤗 Feel free to [open an issue](https://github.com/Ritika-Agrawal811/github-readme-blog-cards/issues/new/choose) or submit a [pull request](https://github.com/Ritika-Agrawal811/github-readme-blog-cards/compare) if you have a way to improve this project.

Make sure your request is meaningful and you have tested the app locally before submitting a pull request.

This documentation contains a set of guidelines to help you during the contribution process.

## ⚙️ Installing Requirements

- [PHP 8.2+](https://www.apachefriends.org/index.html)
- [Composer](https://getcomposer.org)
- [Docker](https://www.docker.com/products/docker-desktop/) (recommended)
- [Inkscape](https://inkscape.org/) (optional, for contributors who want to preview or export SVGs locally)

### Linux

```bash
sudo apt-get install php
sudo apt-get install php-curl
sudo apt-get install composer
sudo apt-get install inkscape
```

### Windows

Install PHP from [XAMPP](https://www.apachefriends.org/index.html) or [php.net](https://windows.php.net/download)

[▶ How to install and run PHP using XAMPP (Windows)](https://www.youtube.com/watch?v=K-qXW9ymeYQ)

[📥 Download Composer](https://getcomposer.org/download/)

## 🛠 Set up the project locally

If you need some help regarding the basics of Git and GitHub, kindly refer to these articles :point_down:

- [Forking a Repo](https://help.github.com/en/github/getting-started-with-github/fork-a-repo)
- [Cloning a Repo](https://docs.github.com/en/repositories/creating-and-managing-repositories/cloning-a-repository)
- [How to create a Pull Request](https://opensource.com/article/19/7/create-pull-request-github)
- [Getting started with Git and GitHub](https://towardsdatascience.com/getting-started-with-git-and-github-6fcd0f2d4ac6)
- [Learn GitHub from Scratch](https://github.com/githubtraining/introduction-to-github)

### Step 1 : Fork the Project 🍴

- Click the Fork button at the top-right of the repository page.
- This will create a copy of this repository under your GitHub profile.

<br/>

![fork button](https://res.cloudinary.com/djix6uusx/image/upload/v1755518567/fork-repo_qwg4oo.png)

<br/>

### Step 2 : Clone and Setup Remotes 📥

- Now clone your forked repository

```bash
git clone https://github.com/<your-username>/github-readme-blog-cards.git
```

<br/>

![clone button](https://res.cloudinary.com/djix6uusx/image/upload/v1755602082/clone-repo_y1ed5z.png)

<br/>

When you clone your fork, Git will automatically set the **origin remote** to point to your forked repository (the copy under your GitHub account).

- You can verify this by running:

```bash
git remote -v
```

You should see something like :

```bash
origin    https://github.com/<your-username>/github-readme-blog-cards.git (fetch)
origin    https://github.com/<your-username>/github-readme-blog-cards.git (push)
```

This means your local copy is connected to your fork.

- Next, you need to add the **upstream remote** that points to the _original repository_. This allows you to keep your fork updated with the latest changes.

```bash
cd github-readme-blog-cards
git remote add upstream https://github.com/Ritika-Agrawal811/github-readme-blog-cards.git
```

- Now if you check again with git remote -v, you should see:

```bash
origin    https://github.com/<your-username>/github-readme-blog-cards.git (fetch)
origin    https://github.com/<your-username>/github-readme-blog-cards.git (push)
upstream  https://github.com/Ritika-Agrawal811/github-readme-blog-cards.git (fetch)
upstream  https://github.com/Ritika-Agrawal811/github-readme-blog-cards.git (push)
```

- `origin` → always points to your fork (your copy, where you push branches).

- `upstream` → always points to the original repo (this project, where you eventually open a PR).

### Step 3 : Running the Application 🏃

You can run the project in two ways:

#### Option 1 : Using Composer and PHP

1.  Install all the required dependencies

```bash
composer install
```

2. Start the local development server on port `8000`

```bash
composer start
```

3. Open the app in your browser at:

http://localhost:8000/?url=https://medium.com/@RitikaAgrawal08/diving-deep-into-z-index-property-d60e3443f4ec

This will launch the project locally and let you test it right away! :fire:

👉 To test different cards, simply change the value passed to the `url` query parameter.

#### Option 2 : Using Docker (recommended)

Make sure you have Docker Desktop (or Docker Engine) installed and running.

1. Run the application

```bash
docker-compose up --build
```

This launches the app on port `8080`.

2. Open the app in your browser at:

http://localhost:8080/?url=https://medium.com/@RitikaAgrawal08/diving-deep-into-z-index-property-d60e3443f4ec

3. Stop and remove containers (without deleting images)

```bash
docker-compose down
```

⚠️ **Important :** Running via Docker is recommended for contributors because it ensures your environment matches production.

## Submitting Contributions 👨‍💻

### Step 1 : Find an issue

- Take a look at the existing issues or create your **own** issues! You may use one of the [issue templates](https://github.com/Ritika-Agrawal811/github-readme-blog-cards/issues/new/choose) to create your own issue.

![issues tab](https://res.cloudinary.com/djix6uusx/image/upload/v1756027340/issues-tab_gibvtu.png)

### Step 2 : Setup the application

Before you start working on your first contribution, make sure you have the project running locally. Follow the setup instructions from the previous section to install and run the application on your system.

- If you have already forked the project, update your copy before working.

```bash
# Make sure you're on main
git checkout main

# Fetch latest changes from original repo
git fetch upstream

# Update your local main branch
git pull upstream main

# Push the updated main to your fork
git push origin main
```

### Step 3 : Create a new branch

Create a new branch. Use its name to identify the issue you're addressing.

```bash
# Create a new branch with the name feature_name and switch to it
git checkout -b feature/feature_name
```

### Step 4 : Work on the issue assigned

- Work on the issue assigned to you.
- Make the required changes or improvements in the codebase.
- Once you’ve made your changes, add them to the branch you created earlier using one of the following commands:

```bash
# Add all changes at once
git add .

# Or add specific files
git add <file-path-1> <file-path-2>
```

### Step 5 : Commit your changes

A commit is like a checkpoint in your project. It saves the changes you’ve staged in the previous step using `git add` command.

- Write a clear and descriptive commit message :

```bash
# This message will get associated with all files you have changed
git commit -m "fix: corrected typo in README"

# Another example
git commit -m "feat: add dark mode toggle in navbar"
```

### Step 6 : Push your branch

- When your changes are successfully committed and follow the project conventions, upload them to your fork :

```bash
# Push your branch to your fork (replace branch_name with your branch)
git push -u origin branch_name
```

### Step 7 : Create a Pull Request

- Go to your forked repository on GitHub.
- Click on “Compare & pull request” for the branch you just pushed.
- Add a clear title and description for your pull request that explains your contribution.
- Double-check your changes, then click “Create pull request”.

Congratulations! You’ve just submitted a Pull Request. Now it’s ready to be merged 🥳

<p align='center'>• • •</p>

#### Thanks for taking the time to contribute! 🎉
