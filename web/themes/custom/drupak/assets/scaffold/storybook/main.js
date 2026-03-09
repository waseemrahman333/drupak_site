/** @type { import('@storybook/server-webpack5').StorybookConfig } */
const config = {
  stories: ["../web/**/*.mdx", "../web/**/*.stories.@(json|yaml|yml)"],
  staticDirs: ['../images'],
  addons: [
    "@storybook/addon-webpack5-compiler-swc",
    "@storybook/addon-essentials",
    "@chromatic-com/storybook",
  ],
  framework: {
    name: "@storybook/server-webpack5",
    options: {},
  },
};
export default config;
