<?php

	/**
	 * Defines Area ID constants which can be used in SQL queries
	 * ID numbers are taken from webset.sped_constructions table stored on SPEDEX Main
	 *
	 *
	 * @copyright 2013, LumenTouch
	 * @author Nick Ignatushko
	 */
	abstract class IDEAAppArea {

		/**
		* Missoury, Special Considerations
		*/
		const SPECIAL_CONS = 164;
		
		/**
		* Idaho, SLD, B. Core Curriculum
		*/
		const ID_SLD_CORE_CURRICULUM = 100;

		/**
		* Idaho, SLD, B. Core Instruction
		*/
		const ID_SLD_CORE_INSTRUCTION = 101;

		/**
		* Idaho, SLD, B. Intervention
		*/
		const ID_SLD_CORE_INTERVENTION = 102;
		
		/**
		* Idaho, SLD, C. Assessments
		*/
		const ID_SLD_CORE_C_ASSESSMENTS = 104;

		/**
		* Idaho, SLD, F. Assessments
		*/
		const ID_SLD_CORE_F_ASSESSMENTS = 108;

		/**
		* Idaho, SLD, E: Supplemental Assessment
		*/
		const ID_SLD_SUPPLEMENT = 106;

		/**
		* Idaho, SLD, D. Strengths and Weaknesses
		*/
		const ID_SLD_STRENGTHS = 105;

		/**
		* Idaho, SLD, Evaluation team
		*/
		const ID_SLD_EVAL_TEAM = 125;

		/**
		* Idaho, SLD, F. Question
		*/
		const ID_SLD_FQUESTION = 107;

		/**
		* Idaho, SLD, LRE forms
		*/
		const ID_LRE_FORMS = 114;
		
		/**
		* Idaho, SLD, Early Childhood Main App
		*/
		const ID_EC_MAIN = 152;

		/**
		* Idaho, SLD, Early Childhood Annual Goals
		*/
		const ID_EC_GOALS = 153;

		/**
		* Idaho, SLD, Early Childhood Annual Goals Objectives
		*/
		const ID_EC_OBJECTIVES = 154;

		/**
		* Idaho, SLD, EC Progress Report
		*/
		const ID_EC_PROGRESS = 159;

		/**
		* Idaho, SLD, Secondary IEP, Transition Activities
		*/
		const ID_SEC_TRANS_ACTIVITIES = 134;

		/**
		* Idaho, SLD, Secondary IEP, Annual Goals
		*/
		const ID_SEC_GOALS = 142;

		/**
		* Idaho, SLD, Secondary IEP, Goals Objectives
		*/
		const ID_SEC_OBJECTIVES = 156;

		/**
		* Idaho, SLD, Secondary IEP, Progress Report
		*/
		const ID_SEC_PROGRESS = 158;

		/**
		* Idaho, SLD, Secondary IEP, Courses of Study
		*/
		const ID_SEC_COURSES_STUDY = 136;

		/**
		* Idaho, SLD, Secondary IEP, Postsecondary Goals
		*/
		const ID_SEC_POST_GOALS = 132;

		/**
		* Idaho, SLD, Secondary IEP, Assessment Summary for Transition Services Planning
		*/
		const ID_SEC_ASSESSMENT_SUMMARY = 128;

		/**
		* Indiana, ILP, Individual Learning Plan
		*/
		const IN_ILP_ELIGIBILITY = 120;

		/**
		* Indiana, IEP, IRead
		*/
		const IN_IREAD = 157;

		/**
		* Texas, ARD, Related Services Dates
		*/
		const TX_ADDITIONAL_DATES = 113;

		/**
		* Missoury, IEP, Total Minutes
		*/
		const TOTAL_MINUTES = 122;

		/**
		* Tennessee, IFSP, Page One - Cover Page
		*/
		const TN_IFSP_COVER_PAGE = 177; 

		/**
		* Tennessee, IFSP, Page Two - Identifying Information
		*/
		const TN_IFSP_IDENT_INFO = 179; 

		/**
		* Tennessee, IFSP, Pages Three And Four - Present Levels Of Development
		*/
		const TN_IFSP_PLEP = 181; 

		/**
		* Tennessee, IFSP, Page Five - Summary Of Family Resources, Priorities, And Concerns Related To Enhancing The Development Of The Child
		*/
		const TN_IFSP_SUMMARY_FAMILY = 183; 

		/**
		* Tennessee, IFSP, Page Six - Outcome/Action Steps
		*/
		const TN_IFSP_OUTCOME_ACTION = 185; 

		/**
		* Tennessee, IFSP, Page Seven - Services
		*/
		const TN_IFSP_SEVICES = 187; 

		/**
		* Tennessee, IFSP, Page Nine - Review/Change Form
		*/
		const TN_IFSP_REVIEW_FORM = 189; 

		/**
		* Tennessee, IFSP, Pages Ten And Eleven - Transition From Part C Services Plan
		*/
		const TN_IFSP_TRANSITION_FORMC = 191; 

		/**
		* Tennessee, IFSP, Page Eight - Outcomes/Services Summary Page
		*/
		const TN_IFSP_OUTCOME_SUMMARY = 201; 

	}

?>
