export type SiteConfig = typeof siteConfig

export const siteConfig = {
  name: 'Next.js + NextUI',
  description: 'Make beautiful websites regardless of your design experience.',

  navItems: [
    {
      label: 'Polls',
      href: '/user/polls',
      requiresAuth: true,
    },
    {
      label: 'Settings',
      href: '/user/settings',
      requiresAuth: true,
    },
    {
      label: 'Logout',
      href: '/user/logout',
      requiresAuth: true,
    },
    {
      label: 'Login',
      href: '/login',
      requiresAuth: false,
    },
  ],

  links: {
    github: 'https://github.com/KDederichs/patreon_polls',
  },
}
