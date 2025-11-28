import { getPermalink } from './permalinks';

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
  sections: [
    {
      title: 'Navigation',
      links: [
        { text: 'Start', href: getPermalink() },
        { text: 'Über mich', href: getPermalink('/ueber-mich') },
        { text: 'Termin buchen', href: getPermalink('/termin-buchen') },
        { text: 'FAQ', href: getPermalink('/#faq') },
      ],
    },
    {
      title: 'Angebote',
      links: [
        { text: 'Darmtherapie', href: getPermalink('/#features') },
        { text: 'Methoden', href: getPermalink('/#methods') },
        { text: 'Kontakt', href: getPermalink('/kontakt') },
      ],
    },
    {
      title: 'Rechtliches',
      links: [
        { text: 'Impressum', href: getPermalink('/impressum') },
        { text: 'Datenschutz', href: getPermalink('/datenschutz') },
        { text: 'AGB', href: getPermalink('/agb') },
        { text: 'Widerrufsbelehrung', href: getPermalink('/widerrufsbelehrung') },
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
      text: 'Folge mir auf Instagram',
      ariaLabel: 'Instagram',
      icon: 'tabler:brand-instagram',
      href: 'https://www.instagram.com/stories/wohl_fuehl_gesundheit/',
    },
  ],
  footNote: `
    <span>© ${new Date().getFullYear()} Wohlfühlgesundheit - Holistische Darmtherapie</span>
  `,
};
