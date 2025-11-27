import { getPermalink } from '../utils/permalinks';

export const headerData = {
  links: [
    {
      text: 'Start',
      href: getPermalink(),
    },
    {
      text: 'Über mich',
      href: getPermalink('/ueber-mich'),
    },
    {
      text: 'Termin buchen',
      href: getPermalink('/termin-buchen'),
    },
    {
      text: 'Fragen?',
      href: getPermalink('/#faq'),
    },
    {
      text: 'Schreib mir',
      href: getPermalink('/kontakt'),
    },
  ],
};

export const footerData = {
  columns: [
    {
      title: 'Navigation',
      links: [
        { text: 'Start', href: getPermalink('/') },
        { text: 'Über mich', href: getPermalink('/ueber-mich') },
        { text: 'Termin buchen', href: getPermalink('/termin-buchen') },
        { text: 'Fragen?', href: getPermalink('/#faq') },
        { text: 'Kontakt', href: getPermalink('/kontakt') },
      ],
    },
  ],
  secondaryLinks: [
    { text: 'Impressum', href: getPermalink('/impressum') },
    { text: 'Datenschutz', href: getPermalink('/datenschutz') },
    { text: 'AGB', href: getPermalink('/agb') },
    { text: 'Widerrufsbelehrung', href: getPermalink('/widerrufsbelehrung') },
  ],
  socialLinks: [
    {
      ariaLabel: 'Instagram',
      icon: 'tabler:brand-instagram',
      href: 'https://www.instagram.com/stories/wohl_fuehl_gesundheit/',
    },
  ],
  footNote: `
    © ${new Date().getFullYear()} Wohlfühlgesundheit - Holistische Darmtherapie
  `,
};
