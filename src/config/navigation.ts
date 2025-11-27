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
  sections: [
    {
      title: 'Navigation',
      links: [
        { text: 'Start', href: getPermalink(), icon: 'tabler:home' },
        { text: 'Über mich', href: getPermalink('/ueber-mich'), icon: 'tabler:user-heart' },
        { text: 'Termin buchen', href: getPermalink('/termin-buchen'), icon: 'tabler:calendar-check' },
        { text: 'FAQ', href: getPermalink('/#faq'), icon: 'tabler:help-circle' },
      ],
    },
    {
      title: 'Angebote',
      links: [
        { text: 'Darmtherapie', href: getPermalink('/#leistungen'), icon: 'tabler:heart-handshake' },
        { text: 'Beratung', href: getPermalink('/#leistungen'), icon: 'tabler:message-circle-2' },
        { text: 'Kontakt', href: getPermalink('/kontakt'), icon: 'tabler:mail' },
      ],
    },
    {
      title: 'Rechtliches',
      links: [
        { text: 'Impressum', href: getPermalink('/impressum'), icon: 'tabler:file-text' },
        { text: 'Datenschutz', href: getPermalink('/datenschutz'), icon: 'tabler:shield-lock' },
        { text: 'AGB', href: getPermalink('/agb'), icon: 'tabler:file-description' },
        { text: 'Widerrufsbelehrung', href: getPermalink('/widerrufsbelehrung'), icon: 'tabler:rotate-clockwise-2' },
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
