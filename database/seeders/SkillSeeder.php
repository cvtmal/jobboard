<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $skills = [
            '.NET', 'ASP.NET', 'Azure', 'PHP', 'Laravel', 'Symfony', 'Active Directory', 'Scrum',
            'ITIL', 'JavaScript', 'TypeScript', 'React', 'Vue.js', 'Angular', 'Next.js', 'Nuxt.js',
            'Docker', 'Kubernetes', 'AWS', 'Google Cloud', 'DevOps', 'CI/CD', 'Jenkins', 'GitHub Actions',
            'Python', 'Django', 'Flask', 'Java', 'Spring', 'C#', 'C++', 'SQL', 'NoSQL', 'MongoDB',
            'MySQL', 'PostgreSQL', 'Redis', 'Git', 'GitHub', 'GitLab', 'Bitbucket', 'Jira',
            'Linux', 'Windows Server', 'Networking', 'Security', 'Agile', 'Waterfall', 'Kanban',
            'Confluence', 'Node.js', 'Express.js', 'REST API', 'GraphQL', 'gRPC', 'Microservices',
            'Machine Learning', 'AI', 'Data Science', 'Big Data', 'Hadoop', 'Spark', 'Elasticsearch',
            'Swift', 'Kotlin', 'Flutter', 'React Native', 'Unity', 'Unreal Engine', 'Salesforce', 'SAP',
            'Oracle', 'SharePoint', 'Power BI', 'Tableau', 'Excel', 'Power Platform', 'Terraform',
            'Ansible', 'Chef', 'Puppet', 'Ruby', 'Ruby on Rails', 'Go', 'Rust', 'Shell Scripting',
            'PowerShell', 'Bash', 'Test Automation', 'Selenium', 'Cypress', 'Jest', 'PHPUnit', 'Pest',
            'xUnit', 'JUnit', 'Tailwind CSS', 'Bootstrap', 'Material UI', 'Inertia.js', 'Livewire',
            'Alpine.js', 'jQuery', 'Webpack', 'Vite', 'Svelte', 'WordPress', 'Drupal', 'Magento',
            'Shopify', 'WooCommerce', 'SEO', 'UI/UX Design', 'Figma', 'Adobe XD', 'Photoshop',
            'Illustrator', 'Business Analysis', 'Product Management', 'SaaS', 'E-commerce',
        ];

        foreach ($skills as $skillName) {
            Skill::create([
                'name' => $skillName,
                'slug' => Str::slug($skillName),
                'active' => true,
            ]);
        }
    }
}
