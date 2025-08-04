<?php

declare(strict_types=1);

namespace App\Enums;

enum JobCategory: string
{
    case SoftwareEngineering = 'software_engineering';
    case DevOps = 'devops';
    case DataScience = 'data_science';
    case Cybersecurity = 'cybersecurity';
    case CloudComputing = 'cloud_computing';
    case NetworkEngineering = 'network_engineering';
    case SystemAdministration = 'system_administration';
    case DatabaseAdministration = 'database_administration';
    case ITSupport = 'it_support';
    case UIUXDesign = 'uiux_design';
    case ProductManagement = 'product_management';
    case QATesting = 'qa_testing';
    case MachineLearningAI = 'machine_learning_ai';
    case MobileDevelopment = 'mobile_development';
    case WebDevelopment = 'web_development';
    case ITProjectManagement = 'it_project_management';
    case ITArchitecture = 'it_architecture';
    case EmbeddedSystemsIoT = 'embedded_systems_iot';
    case BlockchainCrypto = 'blockchain_crypto';
    case GameDevelopment = 'game_development';

    /**
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $case) => [
            $case->value => $case->label(),
        ])->all();
    }

    public function label(): string
    {
        return match ($this) {
            self::SoftwareEngineering => 'Software Engineering/Development',
            self::DevOps => 'DevOps & Site Reliability Engineering',
            self::DataScience => 'Data Science & Analytics',
            self::Cybersecurity => 'Cybersecurity & Information Security',
            self::CloudComputing => 'Cloud Computing & Infrastructure',
            self::NetworkEngineering => 'Network Engineering',
            self::SystemAdministration => 'System Administration',
            self::DatabaseAdministration => 'Database Administration & Engineering',
            self::ITSupport => 'IT Support & Help Desk',
            self::UIUXDesign => 'UI/UX Design',
            self::ProductManagement => 'Product Management',
            self::QATesting => 'QA & Testing',
            self::MachineLearningAI => 'Machine Learning & AI',
            self::MobileDevelopment => 'Mobile Development',
            self::WebDevelopment => 'Web Development',
            self::ITProjectManagement => 'IT Project Management',
            self::ITArchitecture => 'IT Architecture',
            self::EmbeddedSystemsIoT => 'Embedded Systems & IoT',
            self::BlockchainCrypto => 'Blockchain & Cryptocurrency',
            self::GameDevelopment => 'Game Development',
        };
    }
}
