import { test, expect } from '@playwright/test';
import { login, logout, getTestCredentials } from './helpers/auth.helper';
import { expectOnDashboard } from './helpers/assertions.helper';

test.describe('Authentication', () => {
  test('should login successfully with valid credentials', async ({ page }) => {
    const { email, password } = getTestCredentials();

    await page.goto('/login');
    await expect(page.locator('h1, .card-header')).toContainText('Login');

    await page.fill('#email', email);
    await page.fill('#password', password);

    await page.click('button[type="submit"]');

    await expectOnDashboard(page);
    await expect(page.locator('h2:has-text("My Dashboard")')).toBeVisible();
  });

  test('should show error with invalid credentials', async ({ page }) => {
    await page.goto('/login');

    await page.fill('#email', 'invalid@example.com');
    await page.fill('#password', 'wrongpassword');

    await page.click('button[type="submit"]');

    await expect(page.locator('.alert-danger').first()).toBeVisible({ timeout: 5000 });
  });

  test('should logout successfully', async ({ page }) => {
    const { email, password } = getTestCredentials();

    await login(page, email, password);
    await expectOnDashboard(page);

    await logout(page);

    await page.goto('/login');
    await expect(page.locator('h1, .card-header')).toContainText('Login');

    await page.goto('/home');
    await expect(page).toHaveURL(/.*\/login/);
  });

  test('should redirect to login when accessing protected route without auth', async ({ page }) => {
    await page.goto('/home');

    await expect(page).toHaveURL(/.*\/login/);
  });

  test('should remember user session after page reload', async ({ page }) => {
    const { email, password } = getTestCredentials();

    await login(page, email, password);
    await expectOnDashboard(page);

    await page.reload();

    await expectOnDashboard(page);
    await expect(page.locator('h2:has-text("My Dashboard")')).toBeVisible();
  });
});

