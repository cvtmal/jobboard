import React, { useState } from 'react';
import { Head, useForm } from '@inertiajs/react';
import CompanyLayout from '@/layouts/company-layout';
import { Auth } from '@/types';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import {
  Trash2,
  Plus,
  ToggleLeft,
  Circle,
  CheckSquare,
  Calendar,
  Hash,
  Paperclip,
  Minus,
} from 'lucide-react';
import { useAppearance } from '@/hooks/use-appearance';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';

interface Question {
  id: string;
  text: string;
  requirement: 'optional' | 'required' | 'knockout';
  answerType: 'yes/no' | 'single-choice' | 'multiple-choice' | 'date' | 'number' | 'file-upload' | 'short-text';
  choices?: string[];
}

interface PredefinedQuestion {
  id: string;
  label: string;
  answerType: Question['answerType'];
  defaultText: string;
  choices?: string[];
}

interface Props {
  auth: Auth;
  jobListing: {
    id: number;
    title: string;
    [key: string]: any;
  };
}

export default function Screening({ jobListing, auth }: Props) {
  const { appearance } = useAppearance();
  const isDarkMode = appearance === 'dark' || (appearance === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);

  const predefinedQuestions: PredefinedQuestion[] = [
    { id: 'start-date', label: 'Start date', answerType: 'date' as const, defaultText: 'When are you available to start working with us?' },
    { id: 'salary', label: 'Salary expectation', answerType: 'number' as const, defaultText: 'What is your expected yearly compensation in CHF?' },
    { id: 'work-auth', label: 'Work authorization', answerType: 'yes/no' as const, defaultText: 'Are you currently legally permitted to work in Switzerland?' },
    { id: 'current-city', label: 'Current city', answerType: 'short-text' as const, defaultText: 'What city do you currently live in?' },
    { id: 'drivers-license', label: 'Driver\'s license', answerType: 'yes/no' as const, defaultText: 'Do you have a valid driver\'s license?' },
    { id: 'visa', label: 'Visa status', answerType: 'yes/no' as const, defaultText: 'Will you now or in the future require sponsorship for employment visa status?' },
    { id: 'onsite', label: 'Onsite work', answerType: 'yes/no' as const, defaultText: 'Are you willing to work onsite?' },
    { id: 'remote', label: 'Remote work', answerType: 'yes/no' as const, defaultText: 'Are you willing to work remotely?' },
    {
      id: 'german',
      label: 'German proficiency',
      answerType: 'single-choice' as const,
      defaultText: 'What is your level of German proficiency?',
      choices: [
        'None',
        'Elementary proficiency',
        'Limited working proficiency',
        'Professional working proficiency',
        'Full professional working proficiency',
        'Native or bilingual proficiency'
      ]
    },
    {
      id: 'french',
      label: 'French proficiency',
      answerType: 'single-choice' as const,
      defaultText: 'What is your level of French proficiency?',
      choices: [
        'None',
        'Elementary proficiency',
        'Limited working proficiency',
        'Professional working proficiency',
        'Full professional working proficiency',
        'Native or bilingual proficiency'
      ]
    },
    {
      id: 'italian',
      label: 'Italian proficiency',
      answerType: 'single-choice' as const,
      defaultText: 'What is your level of Italian proficiency?',
      choices: [
        'None',
        'Elementary proficiency',
        'Limited working proficiency',
        'Professional working proficiency',
        'Full professional working proficiency',
        'Native or bilingual proficiency'
      ]
    },
    {
      id: 'english',
      label: 'English proficiency',
      answerType: 'single-choice' as const,
      defaultText: 'What is your level of English proficiency?',
      choices: [
        'None',
        'Elementary proficiency',
        'Limited working proficiency',
        'Professional working proficiency',
        'Full professional working proficiency',
        'Native or bilingual proficiency'
      ]
    },
    {
      id: 'employment',
      label: 'Employment type',
      answerType: 'single-choice' as const,
      defaultText: 'What type of employment are you looking for?',
      choices: [
        'Full-time',
        'Part-time',
        'Both'
      ]
    },
    { id: 'shift', label: 'Shift work', answerType: 'yes/no' as const, defaultText: 'Are you willing to work in shifts?' },
  ];

  const initialQuestions = [
    {
      id: 'start-date',
      text: predefinedQuestions.find(q => q.id === 'start-date')?.defaultText || '',
      requirement: 'optional' as const,
      answerType: 'date' as const,
    },
    {
      id: 'salary',
      text: predefinedQuestions.find(q => q.id === 'salary')?.defaultText || '',
      requirement: 'optional' as const,
      answerType: 'number' as const,
    }
  ];

  const requirementOptions = {
    optional: {
      label: 'Optional',
      description: 'Candidate is allowed to skip it'
    },
    required: {
      label: 'Required',
      description: 'Candidate will have to answer it'
    },
    knockout: {
      label: 'Knockout',
      description: 'Rejection email will be sent next day to the candidates who don\'t meet your requirements'
    }
  };

  const { data, setData, post, processing, errors } = useForm({
    application_documents: {
      cv: 'required',
      cover_letter: 'optional',
    },
    screening_questions: initialQuestions,
  });

  const [questions, setQuestions] = useState<Question[]>(initialQuestions);
  const [customQuestion, setCustomQuestion] = useState('');

  const addPredefinedQuestion = (question: PredefinedQuestion) => {
    if (questions.some(q => q.id === question.id)) {
      console.log('Question already added');
      return;
    }

    const newQuestion: Question = {
      id: question.id,
      text: question.defaultText,
      requirement: 'optional' as const,
      answerType: question.answerType,
      choices: question.choices,
    };

    const updatedQuestions = [...questions, newQuestion];
    setQuestions(updatedQuestions);
    setData('screening_questions', updatedQuestions);
  };

  const addCustomQuestion = () => {
    if (!customQuestion.trim()) {
      return;
    }

    const newId = `custom-${Date.now()}`;
    const newQuestion: Question = {
      id: newId,
      text: customQuestion,
      requirement: 'optional' as const,
      answerType: 'single-choice' as const,
    };

    const updatedQuestions = [...questions, newQuestion];
    setQuestions(updatedQuestions);
    setData('screening_questions', updatedQuestions);
    setCustomQuestion('');
  };

  const removeQuestion = (id: string) => {
    const filteredQuestions = questions.filter(q => q.id !== id);
    setQuestions(filteredQuestions);
    setData('screening_questions', filteredQuestions);
  };

  const updateRequirement = (id: string, requirement: Question['requirement']) => {
    const updatedQuestions = questions.map(q => {
      if (q.id === id) {
        return {
          ...q,
          requirement
        };
      }
      return q;
    });

    setQuestions(updatedQuestions);
    setData('screening_questions', updatedQuestions);
  };

  const updateAnswerType = (id: string, answerType: Question['answerType']) => {
    const updatedQuestions = questions.map(q => {
      if (q.id === id) {
        return {
          ...q,
          answerType
        };
      }
      return q;
    });

    setQuestions(updatedQuestions);
    setData('screening_questions', updatedQuestions);
  };

  const updateQuestionText = (id: string, text: string) => {
    const updatedQuestions = questions.map(q => {
      if (q.id === id) {
        return { ...q, text };
      }
      return q;
    });

    setQuestions(updatedQuestions);
    setData('screening_questions', updatedQuestions);
  };

  const getAnswerTypeIcon = (type: Question['answerType']) => {
    switch (type) {
      case 'short-text':
        return <Minus className="h-4 w-4 mr-2 text-primary" />;
      case 'yes/no':
        return <ToggleLeft className="h-4 w-4 mr-2 text-primary" />;
      case 'single-choice':
        return <Circle className="h-4 w-4 mr-2 text-primary" />;
      case 'multiple-choice':
        return <CheckSquare className="h-4 w-4 mr-2 text-primary" />;
      case 'date':
        return <Calendar className="h-4 w-4 mr-2 text-primary" />;
      case 'number':
        return <Hash className="h-4 w-4 mr-2 text-primary" />;
      case 'file-upload':
        return <Paperclip className="h-4 w-4 mr-2 text-primary" />;
      default:
        return null;
    }
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    post(route('company.job-listings.screening.update', jobListing.id));
  };

  return (
    <CompanyLayout>
      <Head title="Add Screening Questions" />

      <div className="py-12">
        <div className="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
          <form onSubmit={handleSubmit}>
            <div className="mb-8">
              <h1 className="text-3xl font-bold tracking-tight">Application Documents</h1>
              <p className="mt-2 text-muted-foreground">
                Customize the document requirements for this job.
              </p>
            </div>
            {/* Application Documents */}
            <Card className="mb-6">
              <CardContent className="space-y-4">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div className="space-y-2">
                    <Label htmlFor="cv">CV / Resume</Label>
                    <Select
                      value={data.application_documents.cv}
                      onValueChange={(value) => setData('application_documents', {
                        ...data.application_documents,
                        cv: value
                      })}
                    >
                      <SelectTrigger>
                        <SelectValue placeholder="Select option" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem value="required">Required</SelectItem>
                        <SelectItem value="optional">Optional</SelectItem>
                        <SelectItem value="hidden">Hidden</SelectItem>
                      </SelectContent>
                    </Select>
                  </div>

                  <div className="space-y-2">
                    <Label htmlFor="cover_letter">Cover Letter</Label>
                    <Select
                      value={data.application_documents.cover_letter}
                      onValueChange={(value) => setData('application_documents', {
                        ...data.application_documents,
                        cover_letter: value
                      })}
                    >
                      <SelectTrigger>
                        <SelectValue placeholder="Select option" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem value="required">Required</SelectItem>
                        <SelectItem value="optional">Optional</SelectItem>
                        <SelectItem value="hidden">Hidden</SelectItem>
                      </SelectContent>
                    </Select>
                  </div>
                </div>
              </CardContent>
            </Card>

            <div className="mb-8">
              <div className="flex items-center gap-2">
                <h1 className="text-3xl font-bold tracking-tight">Screening Questions</h1>
                <Badge variant="secondary">Optional</Badge>
              </div>
              <p className="mt-2 text-muted-foreground">
                Add screening questions and application documents to find the best candidates for {jobListing.title}.
              </p>
            </div>

            {/* Questions List */}
            {questions.length > 0 && (
              <div className="space-y-4 mb-6">
                {questions.map((question) => (
                  <Card key={question.id} className="border border-border">
                    <div className="flex items-center justify-between p-4 border-b">
                      <h3 className="text-lg font-medium">
                        {predefinedQuestions.find(q => q.id === question.id)?.label || 'Custom Question'}
                      </h3>
                      <div className="flex items-center">
                        <Select
                          value={question.requirement}
                          onValueChange={(value) => updateRequirement(question.id, value as Question['requirement'])}
                        >
                          <SelectTrigger className="w-[120px]">
                            <SelectValue>
                              {question.requirement ? requirementOptions[question.requirement]?.label : 'Select'}
                            </SelectValue>
                          </SelectTrigger>
                          <SelectContent>
                            <SelectItem value="optional">
                              <div className="flex flex-col">
                                <span>{requirementOptions.optional.label}</span>
                                <span className="text-xs text-muted-foreground">{requirementOptions.optional.description}</span>
                              </div>
                            </SelectItem>
                            <SelectItem value="required">
                              <div className="flex flex-col">
                                <span>{requirementOptions.required.label}</span>
                                <span className="text-xs text-muted-foreground">{requirementOptions.required.description}</span>
                              </div>
                            </SelectItem>
                            <SelectItem value="knockout">
                              <div className="flex flex-col">
                                <span>{requirementOptions.knockout.label}</span>
                                <span className="text-xs text-muted-foreground">{requirementOptions.knockout.description}</span>
                              </div>
                            </SelectItem>
                          </SelectContent>
                        </Select>
                        <Button
                          type="button"
                          variant="ghost"
                          size="icon"
                          className="ml-2"
                          onClick={() => removeQuestion(question.id)}
                        >
                          <Trash2 className="h-4 w-4 text-red-500" />
                        </Button>
                      </div>
                    </div>
                    <CardContent className="p-4">
                      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div className="space-y-2">
                          <Label>Question</Label>
                          <Input
                            value={question.text}
                            onChange={(e) => updateQuestionText(question.id, e.target.value)}
                            className="w-full"
                          />
                        </div>
                        <div className="space-y-2">
                          <Label>Answer type</Label>
                          <Select
                            value={question.answerType}
                            onValueChange={(value) => updateAnswerType(question.id, value as Question['answerType'])}
                          >
                            <SelectTrigger className="flex items-center">
                              {getAnswerTypeIcon(question.answerType)}
                              <div className="ml-1">
                                {question.answerType === 'yes/no' ? 'Yes / No' :
                                  question.answerType === 'single-choice' ? 'Single choice' :
                                    question.answerType === 'multiple-choice' ? 'Multiple choice' :
                                      question.answerType === 'date' ? 'Date' :
                                        question.answerType === 'number' ? 'Number' :
                                          question.answerType === 'file-upload' ? 'File upload' :
                                            question.answerType === 'short-text' ? 'Short text' :
                                              question.answerType}
                              </div>
                            </SelectTrigger>
                            <SelectContent>
                              <SelectItem value="short-text">
                                <div className="flex items-center">
                                  <Minus className="h-4 w-4 mr-2 text-primary" />
                                  <span>Short text</span>
                                </div>
                              </SelectItem>
                              <SelectItem value="yes/no">
                                <div className="flex items-center">
                                  <ToggleLeft className="h-4 w-4 mr-2 text-primary" />
                                  <span>Yes / No</span>
                                </div>
                              </SelectItem>
                              <SelectItem value="single-choice">
                                <div className="flex items-center">
                                  <Circle className="h-4 w-4 mr-2 text-primary" />
                                  <span>Single choice</span>
                                </div>
                              </SelectItem>
                              <SelectItem value="multiple-choice">
                                <div className="flex items-center">
                                  <CheckSquare className="h-4 w-4 mr-2 text-primary" />
                                  <span>Multiple choice</span>
                                </div>
                              </SelectItem>
                              <SelectItem value="date">
                                <div className="flex items-center">
                                  <Calendar className="h-4 w-4 mr-2 text-primary" />
                                  <span>Date</span>
                                </div>
                              </SelectItem>
                              <SelectItem value="number">
                                <div className="flex items-center">
                                  <Hash className="h-4 w-4 mr-2 text-primary" />
                                  <span>Number</span>
                                </div>
                              </SelectItem>
                              <SelectItem value="file-upload">
                                <div className="flex items-center">
                                  <Paperclip className="h-4 w-4 mr-2 text-primary" />
                                  <span>File upload</span>
                                </div>
                              </SelectItem>
                            </SelectContent>
                          </Select>
                        </div>
                      </div>
                      {question.answerType === 'single-choice' && question.choices && question.choices.length > 0 && (
                        <div className="mt-4 space-y-4">
                          <Label>Options</Label>
                          <RadioGroup
                            value={question.choices[0]}
                            onValueChange={() => { }}
                          >
                            {question.choices.map((choice, index) => (
                              <div key={index} className="flex items-center space-x-2 border p-3 rounded-md">
                                <RadioGroupItem value={choice} id={`${question.id}-option-${index}`} />
                                <Label htmlFor={`${question.id}-option-${index}`} className="cursor-pointer flex-grow">
                                  {choice}
                                </Label>
                              </div>
                            ))}
                          </RadioGroup>
                        </div>
                      )}
                      {question.answerType === 'single-choice' && (!question.choices || question.choices.length === 0) && (
                        <div className="space-y-2">
                          <Label>Choices</Label>
                          <ul>
                            <li className="flex items-center gap-2">
                              <Input
                                type="text"
                                value=""
                                onChange={(e) => {
                                  const updatedChoices = [...(question.choices || []), e.target.value];
                                  const updatedQuestion = { ...question, choices: updatedChoices };
                                  const updatedQuestions = questions.map(q => q.id === question.id ? updatedQuestion : q);
                                  setQuestions(updatedQuestions);
                                  setData('screening_questions', updatedQuestions);
                                }}
                                className="w-full"
                              />
                              <Button
                                type="button"
                                variant="ghost"
                                size="icon"
                                onClick={() => {
                                  const updatedChoices = [...(question.choices || []), ''];
                                  const updatedQuestion = { ...question, choices: updatedChoices };
                                  const updatedQuestions = questions.map(q => q.id === question.id ? updatedQuestion : q);
                                  setQuestions(updatedQuestions);
                                  setData('screening_questions', updatedQuestions);
                                }}
                              >
                                <Plus className="h-4 w-4 text-primary" />
                              </Button>
                            </li>
                          </ul>
                        </div>
                      )}
                    </CardContent>
                  </Card>
                ))}
              </div>
            )}

            {/* Screening Questions */}
            <Card className="mb-6">
              <CardContent className="space-y-4">
                {/* Question chips */}
                <div className="flex flex-wrap gap-2 mb-4">
                  {predefinedQuestions
                    .filter(question => !questions.some(q => q.id === question.id))
                    .map((question) => (
                      <button
                        key={question.id}
                        type="button"
                        onClick={() => addPredefinedQuestion(question)}
                        className={`inline-flex items-center border rounded-full px-2.5 py-0.5 text-xs font-semibold transition-colors cursor-pointer bg-background hover:bg-primary hover:text-primary-foreground ${isDarkMode ? 'border-border' : 'border-input'
                          }`}
                      >
                        {question.label}
                      </button>
                    ))}
                </div>

                {/* Custom question input */}
                <div className="flex items-center gap-2">
                  <Input
                    type="text"
                    placeholder="Type a custom question"
                    value={customQuestion}
                    onChange={(e) => setCustomQuestion(e.target.value)}
                    className="flex-1"
                  />
                  <Button
                    type="button"
                    onClick={addCustomQuestion}
                    disabled={!customQuestion.trim()}
                    size="sm"
                  >
                    <Plus className="w-4 h-4 mr-1" /> Add
                  </Button>
                </div>

                {/* Empty state message */}
                {questions.length === 0 && (
                  <div className="text-center py-6 text-muted-foreground">
                    No screening questions added yet. Click on a chip above to add questions.
                  </div>
                )}
              </CardContent>
            </Card>

            {/* Submit Section */}
            <div className="flex items-center justify-end gap-4">
              <Button
                type="button"
                variant="outline"
                onClick={() => window.history.back()}
                disabled={processing}
              >
                Back
              </Button>
              <Button type="submit" disabled={processing}>
                Save and Continue
              </Button>
            </div>
          </form>
        </div>
      </div>
    </CompanyLayout>
  );
}
