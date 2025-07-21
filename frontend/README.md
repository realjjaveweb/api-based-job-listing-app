# Simple Vue 3 frontend built&exposed with Vite

## Development

1. `make app_run` should've already run the install&run dev in the container
2. http://localhost:5173

### Linting

- check for errors: `make frontend_lint`
- fix errors & format: `make frontend_lint_fix`

### Room for improvement

- use TypeScript
- add unit tests

### Build for production

1. Build the app
   ```
   make frontend_build
   ```
2. The built files will be in the `dist/` folder.
