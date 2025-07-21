# API-based Job Listing App

## Architecture
Domain-Driven Design (DDD) is probably an overkill for this project,
but is just used to demonstrate the principles and allows easier further development.
Similar goes for using getters for otherwise readonly properties,
I personally prefer ->propName & public readonly, but I understand it's not to everyone's taste.
From PHP8.4 property hooks could be considered as a compromise...

API Note - to be future safe, introducing explicit API output naming of the properties is a good idea,
however I did not do it here to keep the code simple as the app is self-serving for now...

Exceptions note - some exceptions are now propagated to the frontend
for development purposes => improvement could later be made 
to create Backend=>Frontend-specific exceptions to not expose the error content to the frontend
(even though it's not directly displayed to the user, it's still a better practice)

### Main source code structure
#### Backend
```
├── src/
│   ├── Application/
│   ├── Controller/
│   ├── Domain/
│   ├── Dto/
│   └── Infrastructure/
└── tests/
```

#### Frontend
```
├── frontend/
│   ├── components/
│   ├── App.vue         # the main component
│   └── main.js         # entrypoint
```
Rest of the folders are for configuration and app/console support.

## Setup

### Prerequisites
- Docker (modern one with `docker compose` (not <del>`docker-compose`</del>))
- make command available (for Makefile processing)
- Linux/Ubuntu 24

### Installation

1. **Clone the repository and enter the project directory**
   ```
   git clone https://github.com/realjjaveweb/api-based-job-listing-app.git
   cd api-based-job-listing-app
   ```

2. **Create `.env` file and specify following environment variables:**
    - `APP_ENV=dev`
    - `APP_SECRET=...`
    - `RECRUITIS_API_TOKEN=...`
    - (replace `...` with actual values)

2. **Start the application**
   ```
   make app_run
   ```
   - for more commands run `make help` or see the `Makefile`

3. Give it time to start up and build the frontend... (happens in the detached mode)

4. See [Development -> Urls](#urls) section of this readme for the app urls.

_If you happen to run into issues with permissions,
try owning project files by your user - **on host** in the **project root** run:_
```
sudo chown yourUser:yourUserGroup --recursive .
```
_...and then try again._

## Development
### Urls
- Frontend: http://localhost:5173
- Backend API: http://localhost:8080/api
- API Docs (Swagger): http://localhost:8080/api/docs

### Testing & Code Quality
 - `make test` - runs PHPUnit through composer
 - `make quality` - runs PHPStan and PHP CodeSniffer through composer

## API Endpoints

### Jobs
- `GET /api/jobs` - Get all jobs with pagination
- `GET /api/jobs/{id}` - Get specific job details
- `POST /api/jobs/{id}/apply` - Submit job application

### Test
- `GET /api/test` - Health check endpoint


## Contributing

1. Follow DDD or at least clean code principles
2. Write tests for new features
3. Maintain code quality standards (run `make test quality` before committing)
4. Use conventional commit messages AKA human readable commit messages that will actually describe the important changes
    - example of important change: changing a variable name, adding a new method, typo fix in code etc.
    - example of unimportant change: fixing whitespace, formatting code, updating comments etc.


## License

You are free to look&copy parts, not free to redistribute,
your work must exceed 50% of development codebase if you want to use this as a template.
