# CivicGuard

Requirements
* Docker Desktop installed and running
* Git installed
* Terminal Access (WSL/Ubuntu if you are on Windows, or your native terminal if you are on macOS/Linux)


Step-by-Step Installation & Setup

Execute these commands sequentially in your terminal to take the project from zero to fully functional:

Step 1: Clone and Navigate
Clone the repository to your local machine and enter the project folder:
git clone https://github.com/miconvr/civicguard.git
cd civicguard

Step 2: Environment Configuration
Copy the default environment template file to create your local .env:
cp .env.example .env

Step 3: Boot Up Docker Sail Containers
Spin up your local container environment in detached mode:
./vendor/bin/sail up -d

Step 4: Generate Application Key
Generate the unique cryptographic application key inside your container:
./vendor/bin/sail artisan key:generate

Step 5: Install Dependencies & Compile Assets
Install frontend packages and start asset compilation:
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev

Step 6: Setup Database & Seed Data
Run your database migrations and populate initial seed data:
./vendor/bin/sail artisan migrate --seed


Daily Development Workflow

When you return to work on the project on subsequent days, use these quick commands in your terminal:

* Start Working: ./vendor/bin/sail up -d
* Run Vite Frontend: ./vendor/bin/sail npm run dev
* Stop Working: ./vendor/bin/sail stop


Local Services

Once your environment is running, access your application endpoints below:

* Web Application: http://localhost — Main CivicGuard interface (styled with your custom red shield branding)
* phpMyAdmin: http://localhost:8080 — Database management GUI (Log in using credentials from your .env file)