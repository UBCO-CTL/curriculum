# E2E Tests for UBC Curriculum MAP

This directory contains end-to-end tests using Playwright.

## Setup

1. Install dependencies:
```bash
npm install
```

2. Set up test environment variables:
Create a `.env.testing` file (or use environment variables):
```
TEST_BASE_URL=http://localhost:8000
TEST_USER_EMAIL=test@example.com
TEST_USER_PASSWORD=password
```

3. Create a test user in your database with the credentials above.

4. Run tests:
```bash
npx playwright test
```

## Test Structure

- `auth.spec.ts` - Authentication tests
- `programs.spec.ts` - Program CRUD and PLO management
- `courses.spec.ts` - Course CRUD and CLO management
- `course-program-mapping.spec.ts` - Core mapping flow
- `dashboard.spec.ts` - Dashboard views and navigation
- `validation.spec.ts` - Data validation and error handling
- `helpers/` - Shared utilities for tests

## Test Data

All test data is prefixed with `[E2E-${timestamp}]` to:
- Keep it separate from production data
- Make cleanup easier
- Ensure unique identifiers

## Running Specific Tests

```bash
# Run only program tests
npx playwright test programs

# Run in headed mode (see browser)
npx playwright test --headed

# Run in debug mode
npx playwright test --debug

# View test report
npx playwright show-report
```

